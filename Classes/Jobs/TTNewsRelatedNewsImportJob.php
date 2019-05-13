<?php
namespace BeechIt\NewsTtnewsimport\Jobs;

/**
 * Import job for related news (MySQL only!)
 *
 * @package TYPO3
 * @subpackage news_ttnewsimport
 * @author J.Kummer
 * @see https://github.com/ext-news/news_ttnewsimport/issues/31
 */
class TTNewsRelatedNewsImportJob
{
    public function run()
    {
        $result = false;
        $mmData = $this->getMMData();
        if (!empty($mmData)) {
            foreach ($mmData as $data) {
                if (!empty($data)) {
                    // Insert (result/returned value is not checked, no id vil be returned)
                    $this->insertRelation($data);
                    // Update (result/returned value is not checked)
                    $this->updateRelation($data);
                }
            }
            $result = true; // No output/feedback
        }
        return $result;
    }

    /**
     * Get the mm relation data for related news
     *
     * @return array $mmData
     */
    protected function getMMData()
    {
        $mmData = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'ttnmm.uid_local AS ttn_uid_local,
                ttnmm.uid_foreign AS ttn_uid_foreign,
                ttnmm.sorting AS ttn_sorting,
                txn_local.uid AS txn_uid_local,
                txn_foreign.uid AS txn_uid_foreign
            ',
            'tt_news_related_mm AS ttnmm
                JOIN tx_news_domain_model_news AS txn_local ON txn_local.import_id = ttnmm.uid_local
                JOIN tx_news_domain_model_news AS txn_foreign ON txn_foreign.import_id = ttnmm.uid_foreign
            ',
            'ttnmm.tablenames = \'tt_news\'
                AND txn_local.import_source = \'TT_NEWS_IMPORT\'
                AND txn_foreign.import_source = \'TT_NEWS_IMPORT\'
            ',
            'ttnmm.uid_local, ttnmm.uid_foreign' // Exclude tt_news_related_mm duplicates
        );
        return $mmData;
    }

    /**
     * Insert MM data to tx_news_domain_model_news_related_mm
     *
     * @param array $mmData
     * @return void
     */
    protected function insertRelation($mmData)
    {
        if ($mmData['txn_uid_local'] && $mmData['txn_uid_foreign']) {
            $GLOBALS['TYPO3_DB']->exec_INSERTquery(
                'tx_news_domain_model_news_related_mm',
                array(
                    'uid_local' => $mmData['txn_uid_local'],
                    'uid_foreign' => $mmData['txn_uid_foreign'],
                    'sorting_foreign' => $mmData['ttn_sorting'],
                )
            );
        }
    }

    /**
     * Update tx_news_domain_model_news.related
     *
     * @param array $mmData
     * @return void
     */
    protected function updateRelation($mmData)
    {
        // Get current value from news entry for related news
        $news = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'related',
            'tx_news_domain_model_news',
            'uid = ' . $mmData['txn_uid_local']
        );
        // Update news entry - increase value for related news
        if (!empty($news) && isset($news[0]['related'])) {
            $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
                'tx_news_domain_model_news',
                'uid = ' . $mmData['txn_uid_local'],
                array(
                    'related' => $news[0]['related'] +1
                )
            );
        }
    }
}
