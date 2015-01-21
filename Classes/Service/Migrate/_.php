<?php

namespace Plan2net\Vetmed\Updates;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Extbase\Service\FlexFormService;

class NewsPluginsUpdateWizard extends \TYPO3\CMS\Install\Updates\AbstractUpdate {

	/**
	 * @var string
	 */
	protected $title = 'Migrate FE plugins from tt_news to news';

	/** @var FlexFormTools */
	protected $flexFormTools;

	/** @var FlexFormService */
	protected $flexFormService;

	/** @var \TYPO3\CMS\Core\Database\DatabaseConnection */
	protected $db;

	/** @var array */
	protected $errors = array();

	/** @var array */
	protected $categories = array();

	/** @var array */
	protected $valueMap = array(
		'LIST' => 'News->list',
		'LATEST' => 'News->list',
		'SINGLE' => 'News->detail',
		'AMENU' => 'News->dateMenu',
		'SEARCH' => 'News->searchForm',
		'CATMENU' => 'Category->list',
		'datetime' => 'datetime',
		'archivedate' => 'crdate',
		'title' => 'title',
		'asc' => 'asc',
		'desc' => 'desc'
	);

	/** @var array */
	protected $categoryModeMap = array(
		'0' => 'or', // Map default to or to enable override
		'1' => 'or',
		'2' => 'and',
		'-1' => 'notand',
		'-2' => 'notor'
	);

	/** @var array Map everything to '' as current settings are invalid on PROD */
	protected $archiveModeMap = array(
		'0' => '',
		'1' => '', // archived
		'-1' => '' // active
	);

	/** @var array tt_news settings that have no correspondent news setting */
	protected $unconvertedFields = array(
		'template_file',
		'alternatingLayouts',
		'catImageMode',
		'catImageMaxWidth',
		'catImageMaxHeight',
		'maxCatImages',
		'catTextMode',
		'maxCatTexts',
		'firstImageIsPreview',
		'forceFirstImageIsPreview',
		'maxWordsInSingleView',
	);

	/** @var array Default TS config */
	protected $newsFlexConfig = array(
		'data' => array(
			'sDEF' => array(
				'lDEF' => array(
					'switchableControllerActions' => array(
						'vDEF' => 'News->list',
					),
					'settings.orderBy' => array(
						'vDEF' => '',
					),
					'settings.orderDirection' => array(
						'vDEF' => '',
					),
					'settings.dateField' => array(
						'vDEF' => 'datetime',
					),
					'settings.categories' => array(
						'vDEF' => '',
					),
					'settings.categoryConjunction' => array(
						'vDEF' => '',
					),
					'settings.includeSubCategories' => array(
						'vDEF' => '0',
					),
					'settings.archiveRestriction' => array(
						'vDEF' => '',
					),
					'settings.timeRestriction' => array(
						'vDEF' => '',
					),
					'settings.timeRestrictionHigh' =>
						array(
							'vDEF' => '',
						),
					'settings.topNewsRestriction' =>
						array(
							'vDEF' => '',
						),
					'settings.startingpoint' =>
						array(
							'vDEF' => '',
						),
					'settings.recursive' =>
						array(
							'vDEF' => '',
						),
				),
			),
			'additional' =>
				array(
					'lDEF' =>
						array(
							'settings.detailPid' =>
								array(
									'vDEF' => '',
								),
							'settings.listPid' =>
								array(
									'vDEF' => '',
								),
							'settings.backPid' =>
								array(
									'vDEF' => '',
								),
							'settings.limit' =>
								array(
									'vDEF' => '',
								),
							'settings.offset' =>
								array(
									'vDEF' => '',
								),
							'settings.tags' =>
								array(
									'vDEF' => '',
								),
							'settings.hidePagination' =>
								array(
									'vDEF' => '0',
								),
							'settings.topNewsFirst' =>
								array(
									'vDEF' => '0',
								),
							'settings.excludeAlreadyDisplayedNews' =>
								array(
									'vDEF' => '0',
								),
							'settings.disableOverrideDemand' =>
								array(
									'vDEF' => '0',
								),
						),
				),
			'template' =>
				array(
					'lDEF' =>
						array(
							'settings.media.maxWidth' =>
								array(
									'vDEF' => '',
								),
							'settings.media.maxHeight' =>
								array(
									'vDEF' => '',
								),
							'settings.cropMaxCharacters' =>
								array(
									'vDEF' => '',
								),
							'settings.templateLayout' =>
								array(
									'vDEF' => '',
								),
						),
				),
		),
	);

	/**
	 * Checks if an update is needed
	 *
	 * @param string &$description The description for the update
	 * @return boolean TRUE if an update is needed, FALSE otherwise
	 */
	public function checkForUpdate(&$description) {
		$updateNeeded = FALSE;
		$notMigratedRowsCount = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('uid', 'tt_content', 'deleted=0 AND list_type="9"');
		$migratedNewsCount = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('uid', 'tx_news_domain_model_news', 'import_source="TT_NEWS_IMPORT"');
		if ($notMigratedRowsCount > 0 && $migratedNewsCount > 0) {
			$description = 'There are tt_content elements referencing list_type 9 (tt_news)';
			$updateNeeded = TRUE;
		}

		return $updateNeeded;
	}

	/**
	 * Performs the database update.
	 *
	 * @param array &$dbQueries Queries done in this update
	 * @param mixed &$customMessages Custom messages
	 * @return boolean TRUE on success, FALSE on error
	 */
	public function performUpdate(array &$dbQueries, &$customMessages) {
		$this->db = $GLOBALS['TYPO3_DB'];
		$this->flexFormTools = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Configuration\\FlexForm\\FlexFormTools');
		$this->flexFormService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\FlexFormService');
		$this->categories = $this->db->exec_SELECTgetRows('uid, import_id', 'sys_category', 'import_source="TT_NEWS_CATEGORY_IMPORT"', '', '', '', 'import_id');
		$records = $this->getRecordsFromTable();
		foreach ($records as $singleRecord) {
			$this->migrateRecord($singleRecord);
		}
		if ($this->errors) {
			$customMessages = implode(PHP_EOL, $this->errors);
		}
		return TRUE;
	}

	/**
	 * @param array $record
	 * @return void
	 */
	protected function migrateRecord(array $record) {
		$flexDataTtNews = $this->flexFormService->convertFlexFormContentToArray($record['pi_flexform']);
		$flexDataNews = $this->newsFlexConfig;
		if (isset($this->valueMap[$flexDataTtNews['what_to_display']])) {
			$flexDataNews['data']['sDEF']['lDEF']['switchableControllerActions']['vDEF'] = $this->valueMap[$flexDataTtNews['what_to_display']];
			// Set default limit of latest to tt_news default of 3
			if ($flexDataTtNews['what_to_display'] == 'LATEST') {
				$flexDataNews['data']['additional']['lDEF']['settings.limit']['vDEF'] = 3;
				// Set list PID to containing page ID, otherwise no links are generated
			} elseif ($flexDataTtNews['what_to_display'] == 'CATMENU') {
				$flexDataNews['data']['additional']['lDEF']['settings.listPid']['vDEF'] = $record['pid'];
			}
			if (isset($this->valueMap[$flexDataTtNews['listOrderBy']])) {
				$flexDataNews['data']['sDEF']['lDEF']['settings.orderBy']['vDEF'] = $this->valueMap[$flexDataTtNews['listOrderBy']];
			}
			if (isset($this->valueMap[$flexDataTtNews['ascDesc']])) {
				$flexDataNews['data']['sDEF']['lDEF']['settings.orderDirection']['vDEF'] = $this->valueMap[$flexDataTtNews['ascDesc']];
			}
			if (isset($this->categoryModeMap[$flexDataTtNews['categoryMode']])) {
				$flexDataNews['data']['sDEF']['lDEF']['settings.categoryConjunction']['vDEF'] = $this->categoryModeMap[$flexDataTtNews['categoryMode']];
			}
			if ($flexDataTtNews['categorySelection']) {
				$ttCategories = GeneralUtility::trimExplode(',', $flexDataTtNews['categorySelection'], TRUE);
				$categories = array();
				foreach ($ttCategories as $ttCategory) {
					$categories[] = $this->categories[$ttCategory]['uid'];
					unset($ttCategory);
				}
				$flexDataNews['data']['sDEF']['lDEF']['settings.categories']['vDEF'] = implode(',', $categories);
				unset($ttCategories);
				unset($categories);
			}
			if ($flexDataTtNews['useSubCategories']) {
				$flexDataNews['data']['sDEF']['lDEF']['settings.includeSubCategories']['vDEF'] = $flexDataTtNews['useSubCategories'];
			}
			if ($flexDataTtNews['recursive']) {
				$flexDataNews['data']['sDEF']['lDEF']['settings.recursive']['vDEF'] = $flexDataTtNews['recursive'];
			}
			if ($flexDataTtNews['pages']) {
				$flexDataNews['data']['sDEF']['lDEF']['settings.startingpoint']['vDEF'] = $flexDataTtNews['pages'];
			}
			if (isset($this->archiveModeMap[$flexDataTtNews['archive']])) {
				$flexDataNews['data']['sDEF']['lDEF']['settings.archiveRestriction']['vDEF'] = $this->archiveModeMap[$flexDataTtNews['archive']];
			}
			if ($flexDataTtNews['noPageBrowser']) {
				$flexDataNews['data']['additional']['lDEF']['settings.hidePagination']['vDEF'] = $flexDataTtNews['noPageBrowser'];
			}
			if ($flexDataTtNews['listLimit']) {
				$flexDataNews['data']['additional']['lDEF']['settings.limit']['vDEF'] = $flexDataTtNews['listLimit'];
			}
			if ($flexDataTtNews['listStartId']) {
				$flexDataNews['data']['additional']['lDEF']['settings.offset']['vDEF'] = $flexDataTtNews['listStartId'];
			}
			if ($flexDataTtNews['PIDitemDisplay']) {
				$flexDataNews['data']['additional']['lDEF']['settings.detailPid']['vDEF'] = $flexDataTtNews['PIDitemDisplay'];
			}
			if ($flexDataTtNews['backPid']) {
				$flexDataNews['data']['additional']['lDEF']['settings.backPid']['vDEF'] = $flexDataTtNews['backPid'];
			}
			if ($flexDataTtNews['imageMaxWidth']) {
				$flexDataNews['data']['template']['lDEF']['settings.media.maxWidth']['vDEF'] = $flexDataTtNews['imageMaxWidth'];
			}
			if ($flexDataTtNews['imageMaxHeight']) {
				$flexDataNews['data']['template']['lDEF']['settings.media.maxHeight']['vDEF'] = $flexDataTtNews['imageMaxHeight'];
			}
			if ($flexDataTtNews['croppingLenght']) {
				$flexDataNews['data']['template']['lDEF']['settings.cropMaxCharacters']['vDEF'] = $flexDataTtNews['croppingLenght'];
			}
			$ttUid = $record['uid'];
			unset($record['uid']);
			$record['tx_p2categoriesdisplay_category'] = 'ttnewsimport';
			$record['pi_flexform'] = $this->flexArray2Xml($flexDataNews, TRUE);
			$record['list_type'] = 'news_pi1';
			if ($this->db->exec_INSERTquery('tt_content', $record)) {
				$this->db->exec_UPDATEquery('tt_content', 'uid=' . $ttUid, array('deleted' => 1));
			}
			foreach ($this->unconvertedFields as $unconvertedField) {
				if (isset($flexDataTtNews[$unconvertedField]) && $flexDataTtNews[$unconvertedField]) {
					$this->errors[] = '[' . $ttUid . '] unmapped setting in ' . $unconvertedField . ': ' . $flexDataTtNews[$unconvertedField];
				}
			}
		} else {
			$this->errors[] = '[' . $record['uid'] . '] unmapped display type ' . $flexDataTtNews['what_to_display'] . '!';
		}
	}

	/**
	 * Retrieve every record which needs to be processed
	 *
	 * @return array
	 */
	protected function getRecordsFromTable() {
		$sql = $GLOBALS['TYPO3_DB']->SELECTquery(
			'*',
			'tt_content',
			'deleted=0 AND list_type="9"'
		);
		$resultSet = $GLOBALS['TYPO3_DB']->sql_query($sql);
		$records = array();
		if (!$GLOBALS['TYPO3_DB']->sql_error()) {
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultSet)) !== FALSE) {
				$records[] = $row;
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($resultSet);
		}

		return $records;
	}

	public function flexArray2Xml($array, $addPrologue = FALSE) {
		$options = array(
			'parentTagMap' => array(
				'data' => 'sheet',
				'sheet' => 'language',
				'language' => 'field',
				'el' => 'field',
				'field' => 'value',
				'field:el' => 'el',
				'el:_IS_NUM' => 'section',
				'section' => 'itemType'
			),
			'disableTypeAttrib' => 2
		);
		$spaceInd = 4;
		$output = GeneralUtility::array2xml($array, '', 0, 'T3FlexForms', $spaceInd, $options);
		if ($addPrologue) {
			$output = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . LF . $output;
		}

		return $output;
	}
}
