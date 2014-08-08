<?php
namespace BeechIt\NewsTtnewsimport\Service\Import;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Nikolas Hagelstein <nikolas.hagelstein@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tt_news ImportService
 *
 * @package TYPO3
 * @subpackage news_ttnewsimport
 */
class MblNewseventDataProviderService implements \Tx_News_Service_Import_DataProviderServiceInterface, \TYPO3\CMS\Core\SingletonInterface  {

	/**
	 * Get total record count
	 *
	 * @return integer
	 */
	public function getTotalRecordCount() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)',
			'tt_news',
			'deleted=0 AND t3ver_oid = 0 AND t3ver_wsid = 0 AND tx_mblnewsevent_isevent=1'
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
		$importData = array();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
			'tt_news',
			'deleted=0 AND t3ver_oid = 0 AND t3ver_wsid = 0 AND tx_mblnewsevent_isevent=1',
			'',
			'',
			$offset . ',' . $limit
		);

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

			$importData[] = array(
				'uid' => $row['uid'],
				// The following fields cannot be migrated because they are missing in roq_newsevent:
				// tx_mblnewsevent_organizer, tx_mblnewsevent_hasregistration, tx_mblnewsevent_reg*, tx_mblnewsevent_price*
				'tx_mblnewsevent_isevent' => $row['tx_mblnewsevent_isevent'],
				'tx_mblnewsevent_from' => $row['tx_mblnewsevent_from'],
				'tx_mblnewsevent_fromtime' => $row['tx_mblnewsevent_fromtime'],
				'tx_mblnewsevent_to' => $row['tx_mblnewsevent_to'],
				'tx_mblnewsevent_totime' => $row['tx_mblnewsevent_totime'],
				'tx_mblnewsevent_where' => $row['tx_mblnewsevent_where'],

			);
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return $importData;
	}

}