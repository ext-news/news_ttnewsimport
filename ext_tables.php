<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// show tt_news importer only if tt_news is installed
if (true || \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_news')) {
	\Tx_News_Utility_ImportJob::register(
		'BeechIt\\NewsTtnewsimport\\Jobs\\TTNewsNewsImportJob',
		'LLL:EXT:news_ttnewsimport/Resources/Private/Language/locallang_be.xml:ttnews_importer_title',
		'');
	\Tx_News_Utility_ImportJob::register(
		'BeechIt\\NewsTtnewsimport\\Jobs\\TTNewsCategoryImportJob',
		'LLL:EXT:news_ttnewsimport/Resources/Private/Language/locallang_be.xml:ttnewscategory_importer_title',
		'');
}