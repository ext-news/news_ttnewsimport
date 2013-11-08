<?php
$extensionClassesPath = t3lib_extMgm::extPath('news_ttnewsimport') . 'Classes/';

return array(
	'tx_newsttnewsimport_jobs_ttnewscategoryimportjob' => $extensionClassesPath . 'Jobs/TTNewsCategoryImportJob.php',
	'tx_newsttnewsimport_jobs_ttnewsnewsimportjob' => $extensionClassesPath . 'Jobs/TTNewsNewsImportJob.php',
	'tx_newsttnewsimport_service_import_ttnewscategorydataproviderservice' => $extensionClassesPath . 'Service/Import/TTNewsCategoryDataProviderService.php',
	'tx_newsttnewsimport_service_import_ttnewsnewsdataproviderservice' => $extensionClassesPath . 'Service/Import/TTNewsNewsDataProviderService.php',
	'tx_newsttnewsimport_tests_unit_service_import_ttnewsnewsdataproviderservicetest' => $extensionPath . 'Tests/Unit/Service/Import/TTNewsNewsDataProviderServiceTest.php',
);