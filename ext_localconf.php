<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'BeechIt\\NewsTtnewsimport\\Command\\TtNewsImportCommandController';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'BeechIt\\NewsTtnewsimport\\Command\\TtNewsPluginMigrateCommandController';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'BeechIt\\NewsTtnewsimport\\Command\\FalTtNewsMigrationCommandController';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'BeechIt\\NewsTtnewsimport\\Command\\FalMediaShowInPreviewCommandController';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'BeechIt\\NewsTtnewsimport\\Command\\RealUrlUniqueAliasMigrationCommandController';
}
