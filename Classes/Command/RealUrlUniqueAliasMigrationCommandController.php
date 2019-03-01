<?php
namespace BeechIt\NewsTtnewsimport\Command;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Controller to migrate tx_realurl_uniqalias
 * If a lot of similar titles are used it might be a good a idea
 * to migrate the unique aliases to be sure that the same alias is used
 *
 * HINT: Requires tables from realurl version 2.x
 * If realurl version is below 2.x - just switch the uncomment lines in migrateTtNewsRealurlUniqueAliasCommand method
 *
 * @author J.Kummer
 * @author Georg Ringer 2017
 * @see https://github.com/georgringer/news/commit/89e418fb68575116fd9c86e657f8523b1680bd59
 */
class RealUrlUniqueAliasMigrationCommandController extends CommandController
{
    /**
     * First error for execution of SQL statements
     *
     * @var array $error
     */
    protected $error = null;

    /**
     * Command for tt_news tx_realurl_uniqalias migration
     * Requires EXT:realurl version 2.x
     * For EXT:realurl version 1.x switch the uncomment lines...
     *
     * @return void
     * @cli
     */
    public function migrateTtNewsRealurlUniqueAliasCommand()
    {
        // Create temporary table (or drop and recreate)
        $queries[] = 'DROP TABLE IF EXISTS tx_realurl_uniqalias_migration;';
        $queries[] = 'CREATE TABLE tx_realurl_uniqalias_migration LIKE tx_realurl_uniqalias;';
        // Copy
        $queries[] = 'INSERT INTO tx_realurl_uniqalias_migration SELECT * FROM tx_realurl_uniqalias WHERE tablename=\'tt_news\';';
        // Fix it
        $queries[] = 'UPDATE tx_realurl_uniqalias_migration SET value_id = (SELECT tx_news_domain_model_news.uid FROM `tx_news_domain_model_news` WHERE tx_news_domain_model_news.import_id=tx_realurl_uniqalias_migration.value_id),tablename=\'tx_news_domain_model_news\' WHERE tablename=\'tt_news\';';
        // Remove wrong alias (news which have not been imported)
        $queries[] = 'DELETE FROM tx_realurl_uniqalias_migration WHERE tablename=\'tx_news_domain_model_news\' AND value_id=0;';
        // Insert alias back into realurl table
        // RealUrl version < 2.0
        #$queries[] = 'INSERT INTO tx_realurl_uniqalias (tstamp,tablename,field_id,value_alias,value_id,lang,expire) SELECT tstamp,tablename,field_id,value_alias,value_id,lang,expire FROM tx_realurl_uniqalias_migration;';
        // RealUrl version >= 2.0 
        $queries[] = 'INSERT INTO tx_realurl_uniqalias (pid,tablename,field_id,value_alias,value_id,lang,expire) SELECT pid,tablename,field_id,value_alias,value_id,lang,expire FROM tx_realurl_uniqalias_migration;';
        // Drop temporarly table
        $queries[] = 'DROP TABLE tx_realurl_uniqalias_migration;';
        // Run each query
        $countSuccessfulExecutedQueries = 0;
        foreach ($queries as $query) {
            if ($this->executeQuery($query) === false) {
                break;
            }
            $countSuccessfulExecutedQueries++;
        }
        $results = $countSuccessfulExecutedQueries . ' queries of ' . count($queries) . ' executed. ';
        if ($this->error) {
            $results .= 'Break with error! ' . $this->error;
        } else {
            $results .= 'Without errors.';
        }
        echo $results;
    }

    /**
     * Command for tx_realurl_uniqalias into slug/path_segment migration
     * Copies realurl alias to news where path_segment if is empty.
     * Requires, that path_segment was not automaticly filled before!
     * This can still lead in empty slugs field, which can be updated via installtool
     * Use: Upgrade Wizard "Updates slug field 'path_segment' of EXT:news records" (identifier: ’newsSlug’)

     * Or helhum/typo3-console: '$ typo3cms upgrade:wizard newsSlug'
     *
     * @return void
     * @cli
     */
    public function migrateRealurlUniqueAliasIntoPathSegmentCommand()
    {
        $result = '';
        $query = '
UPDATE tx_news_domain_model_news AS n
JOIN tx_realurl_uniqalias AS r ON (n.uid = r.value_id AND n.sys_language_uid = r.lang AND r.tablename = \'tx_news_domain_model_news\')
SET n.path_segment = r.value_alias
WHERE (n.path_segment IS NULL OR n.path_segment = \'\');';
        // Run
        if ($this->executeQuery($query) === false) {
            $result .= 'Break with error! ' . $this->error;
        } else {
            $result .= 'Done.';
        }
        echo $result;
    }

    /**
     * Execute SQL query
     *
     * @param string $query
     * @return bool
     */
    protected function executeQuery($query)
    {
        $resource = $this->getDatabaseConnection()->sql_query($query);
        if ($this->getDatabaseConnection()->sql_error()) {
            $this->error = 'SQL-ERROR for ' . $query . ': ' . htmlspecialchars($this->getDatabaseConnection()->sql_error());
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
