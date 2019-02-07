<?php
namespace BeechIt\NewsTtnewsimport\Command;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Controller to set first news.fal_media to showinpreview
 * Compare to tt_news flexform settings forceFirstImageIsPreview
 * 
 * @author J.Kummer
 */
class FalMediaShowInPreviewCommandController extends CommandController
{
    /**
     * Set news.fal_media first entry value for showinpreview
     *
     * @return void
     * @cli
     */
    public function setFirstFalMediaShowInPreviewCommand()
    {
        $res = $this->getDatabaseConnection()->exec_SELECTquery(
            'uid',
            'sys_file_reference',
            'tablenames = \'tx_news_domain_model_news\' AND fieldname = \'fal_media\' AND deleted = 0',
            'uid_foreign',
            'sorting DESC',
            ''
        );
        $count = 0;
        while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($res)) {
            $this->getDatabaseConnection()->exec_UPDATEquery(
                'sys_file_reference',
                'uid = ' . $row['uid'],
                ['showinpreview' => 1],
                false
            );
            $count++;
        }
        echo $count . ' news.fal_media entries checked for showinpreview.';
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
