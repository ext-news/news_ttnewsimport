<?php
namespace BeechIt\NewsTtnewsimport\Command;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Controller to migrate ext:fal_ttnews file references to ext:news
 * From EXT:news_falttnewsimport 1.0.1
 * 
 * @author J.Kummer
 * @author Ephraim Härer <ephraim.haerer@renolit.com> RENOLIT SE
 */
class FalTtNewsMigrationCommandController extends CommandController
{

    /**
     * Migrate ext:fal_ttnews file references to ext:news
     * Move file references from tt_news to news
     *
     * @return void
     * @cli
     */
    public function migrateFalTtNewsCommand()
    {
        $tables = [0 => 'tt_news', 1 => 'tx_news_domain_model_news', 2 => 'sys_file_reference'];
        $updatedNews = 0;
        $resetFileReferences = ['tx_falttnews_fal_images' => 0, 'tx_falttnews_fal_media' => 0];
        $movedReferences = 0;

        // get news records which were imported
        $news = $this->getDatabaseConnection()->exec_SELECTgetRows(
            'uid, import_id, import_source', $tables[1], 'import_source LIKE \'TT_NEWS_IMPORT\''
        );
        //\TYPO3\CMS\Core\Utility\DebugUtility::debug($news);
        foreach ($news as $n) {
            $updateFields = [];
            // get original tt_news
            $tt_n = $this->getSingleTtNews($n['import_id']);
            if (!empty($tt_n)) {
                if (isset($tt_n['tx_falttnews_fal_images']) && (int) $tt_n['tx_falttnews_fal_images'] > 0) {
                    $updateFields['fal_media'] = $tt_n['tx_falttnews_fal_images'];
                }
                if (isset($tt_n['tx_falttnews_fal_media']) && (int) $tt_n['tx_falttnews_fal_media'] > 0) {
                    $updateFields['fal_related_files'] = $tt_n['tx_falttnews_fal_media'];
                }
            }
            // set the number of file references for news
            if (!empty($updateFields)) {
                $this->getDatabaseConnection()->exec_UPDATEquery($tables[1], 'uid = ' . (int) $n['uid'], $updateFields);
                $updatedNews++;
                $this->getDatabaseConnection()->exec_UPDATEquery($tables[0], 'uid = ' . (int) $n['import_id'], $resetFileReferences);
                // get file references
                $movedRef = $this->rewriteSingleNewsReferences($n['import_id'], $n['uid']);
                $movedReferences = $movedReferences + $movedRef;
            }
        }

        if ($updatedNews > 0) {
            echo 'A total of ' . $movedReferences . ' file references for ' . $updatedNews . ' news has been moved.';
        } else {
            echo 'No file references for moving found, no changes.';
        }
    }

    /**
     * rewrite sys_file_references for a single news entry
     * @param integer $uid_foreign
     * @param integer $uid_foreign_new
     * @return integer
     */
    protected function rewriteSingleNewsReferences($uid_foreign, $uid_foreign_new)
    {
        $movedReferences = 0;
        $table = 'sys_file_reference';
        // get single news record which were imported
        $references = $this->getDatabaseConnection()->exec_SELECTgetRows(
            'uid, uid_foreign, tablenames, fieldname', $table, 'uid_foreign=' . (int) $uid_foreign . ' AND tablenames = \'tt_news\''
        );
        if (count($references) > 0) {
            foreach ($references as $reference) {
                if ($reference['fieldname'] === 'tx_falttnews_fal_images') {
                    $updateFieldsMedia = [
                        'uid_foreign' => $uid_foreign_new,
                        'tablenames' => 'tx_news_domain_model_news',
                        'fieldname' => 'fal_media'
                    ];
                    $this->getDatabaseConnection()->exec_UPDATEquery($table, 'uid=' . (int) $reference['uid'] . ' AND tablenames=\'tt_news\' AND fieldname=\'tx_falttnews_fal_images\'', $updateFieldsMedia);
                    $movedReferences++;
                }
                if ($reference['fieldname'] === 'tx_falttnews_fal_media') {
                    $updateFieldsFiles = [
                        'uid_foreign' => $uid_foreign_new,
                        'tablenames' => 'tx_news_domain_model_news',
                        'fieldname' => 'fal_related_files'
                    ];
                    $this->getDatabaseConnection()->exec_UPDATEquery($table, 'uid=' . (int) $reference['uid'] . ' AND tablenames=\'tt_news\' AND fieldname=\'tx_falttnews_fal_media\'', $updateFieldsFiles);
                    $movedReferences++;
                }
            }
        }
        return $movedReferences;
    }

    /**
     * get single news by uid from table
     * @param integer $uid
     * @param string $table
     * @return array
     */
    protected function getSingleTtNews($uid)
    {
        // get single news record which were imported
        $news = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            'uid, tx_falttnews_fal_images, tx_falttnews_fal_media', 'tt_news', 'uid=' . (int) $uid
        );
        if (isset($news['uid'])) {
            return $news;
        } else {
            return [];
        }
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
