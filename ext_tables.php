<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// show tt_news importer only if tt_news is installed
if (t3lib_extMgm::isLoaded('tt_news')) {
	Tx_News_Utility_ImportJob::register(
		'Tx_News_Jobs_TTNewsNewsImportJob',
		'LLL:EXT:news/Resources/Private/Language/locallang_be.xml:ttnews_importer_title',
		'');
	Tx_News_Utility_ImportJob::register(
		'Tx_News_Jobs_TTNewsCategoryImportJob',
		'LLL:EXT:news/Resources/Private/Language/locallang_be.xml:ttnewscategory_importer_title',
		'');
}