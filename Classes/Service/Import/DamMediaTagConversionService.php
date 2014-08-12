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
 * DAM Media Tag to FAL Link Conversion Service
 *
 * @package TYPO3
 * @subpackage tx_news
 * @author Lorenz Ulrich <lorenz.ulrich@visol.ch>
 */
class DamMediaTagConversionService extends \Tx_News_Domain_Service_AbstractImportService {

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
	 * This converts <media>-Tags of DAM to <link file:XXX> tags referencing a sys_file
	 *
	 * @param array $importData
	 * @param array $importItemOverwrite
	 * @param array $settings
	 * @return void
	 */
	public function import(array $importData, array $importItemOverwrite = array(), $settings = array()) {
		$this->logger->info(sprintf('Starting converting of %s records with DAM media tags', count($importData)));

		/** @var \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler */
		$dataHandler = GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');

		foreach ($importData as $newsRecord) {
			$results = preg_match_all('/<media ([0-9]{1,})(.*?)>(.*?)<\/media>/', $newsRecord['bodytext'], $matches);

			if ($results) {
				foreach ($matches[0] as $key => $mediaTag) {
					$linkTag = '<link file:' . $this->getUidOfSysFileRecord(trim($matches[1][$key])) . ' ' . $matches[2][$key] . '>' . $matches[3][$key] . '</link>';
					$newsRecord['bodytext'] = str_replace($mediaTag, $linkTag, $newsRecord['bodytext']);
				}
				$data = array();
				$data['tx_news_domain_model_news'][$newsRecord['uid']]= array(
					'bodytext' => $newsRecord['bodytext'],
				);
				$dataHandler->start($data, array());
				$dataHandler->process_datamap();
			}
		}

	}


	/**
	 * after migration of DAM-Records we can find sys_file-UID with help of DAM-UID
	 *
	 * @param integer $damUid
	 * @return integer
	 */
	protected function getUidOfSysFileRecord($damUid) {
		$record = $this->databaseConnection->exec_SELECTgetSingleRow(
			'uid',
			'sys_file',
			'_migrateddamuid = ' . (integer) $damUid,
			'', '', ''
		);
		return $record['uid'];
	}

}
