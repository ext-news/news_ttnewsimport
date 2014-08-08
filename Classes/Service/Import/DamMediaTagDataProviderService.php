<?php
namespace BeechIt\NewsTtnewsimport\Service\Import;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * tt_news ImportService
 *
 * @package TYPO3
 * @subpackage news_ttnewsimport
 */
class DamMediaTagDataProviderService implements \Tx_News_Service_Import_DataProviderServiceInterface, \TYPO3\CMS\Core\SingletonInterface  {

	/**
	 * Get total record count
	 *
	 * @return integer
	 */
	public function getTotalRecordCount() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)',
			'tx_news_domain_model_news',
			'bodytext REGEXP ".*<media [0-9]{1,}.*</media>.*" AND deleted = 0'
		);

		list($count) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return (int)$count;
	}

	/**
	 * Get the partial import data, based on offset + limit
	 *
	 * @param integer $offset offset
	 * @param integer $limit limit
	 * @return array
	 */
	public function getImportData($offset = 0, $limit = 50) {

		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,bodytext',
			'tx_news_domain_model_news',
			'bodytext REGEXP ".*<media [0-9]{1,}.*</media>.*" AND deleted = 0',
			'',
			'',
			$offset . ',' . $limit
		);

		return $rows;
	}

}