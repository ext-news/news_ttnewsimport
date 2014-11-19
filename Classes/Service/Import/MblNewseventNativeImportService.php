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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MblNewsevent Import Service
 *
 * @package TYPO3
 * @subpackage tx_news
 * @author Lorenz Ulrich <lorenz.ulrich@visol.ch>, Adaptation Urs Bräm <ub@sturmundbraem.ch>
 */
class MblNewseventNativeImportService extends \Tx_News_Domain_Service_AbstractImportService {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseConnection;

	/**
	 * @var \Tx_News_Domain_Repository_NewsRepository
	 * @inject
	 */
	protected $newsRepository;

	public function __construct() {
		/** @var \TYPO3\CMS\Core\Log\Logger $logger */
		$logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
		$this->logger = $logger;
		$this->databaseConnection = $GLOBALS['TYPO3_DB'];

		parent::__construct();
	}

	/**
	 * Import
	 *
	 * @param array $importData
	 * @param array $importItemOverwrite
	 * @param array $settings
	 * @return void
	 */
	public function import(array $importData, array $importItemOverwrite = array(), $settings = array()) {
		$this->settings = $settings;
		$this->logger->info(sprintf('Starting import for the event data of %s records', count($importData)));

		/** @var \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler */
		$dataHandler = GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
		// keep timestamps untouched
		$dataHandler->dontProcessTransformations = TRUE;

		foreach ($importData as $importItem) {

			$newsRecord = $this->getNewsRecordByImportId($importItem['uid']);

			$data = array();
			$data['tx_news_domain_model_news'][$newsRecord['uid']]= array(
				'datetime' => $importItem['tx_mblnewsevent_from'] + $importItem['tx_mblnewsevent_fromtime'],
				// to preserve the complete endtime, could also be 
				// 'archive' => $importItem['tx_mblnewsevent_to'] + $importItem['tx_mblnewsevent_totime']
				// but tx_news BE has a date field here, so leave it at the date
				'archive' => $importItem['tx_mblnewsevent_to']
			);

			$dataHandler->start($data, array());
			$dataHandler->process_datamap();

		}

	}

	/**
	 * @param integer $ttNewsUid
	 * @return array
	 */
	public function getNewsRecordByImportId($ttNewsUid) {
		return $this->databaseConnection->exec_SELECTgetSingleRow('uid', 'tx_news_domain_model_news', 'import_id=' . (int)$ttNewsUid);
	}

}
