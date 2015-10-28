<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\GeorgRinger\News\Utility\ImportJob::register(
	'BeechIt\\NewsTtnewsimport\\Jobs\\TTNewsNewsImportJob',
	'LLL:EXT:news_ttnewsimport/Resources/Private/Language/locallang_be.xml:ttnews_importer_title',
	'');
\GeorgRinger\News\Utility\ImportJob::register(
	'BeechIt\\NewsTtnewsimport\\Jobs\\TTNewsCategoryImportJob',
	'LLL:EXT:news_ttnewsimport/Resources/Private/Language/locallang_be.xml:ttnewscategory_importer_title',
	'');
\GeorgRinger\News\Utility\ImportJob::register(
	'BeechIt\\NewsTtnewsimport\\Jobs\\MblNewseventImportJob',
	'LLL:EXT:news_ttnewsimport/Resources/Private/Language/locallang_be.xml:mblnewsevent_importer_title',
	'');
\GeorgRinger\News\Utility\ImportJob::register(
	'BeechIt\\NewsTtnewsimport\\Jobs\\DamMediaTagConversionJob',
	'LLL:EXT:news_ttnewsimport/Resources/Private/Language/locallang_be.xml:dammediatag_converter_title',
	'');
