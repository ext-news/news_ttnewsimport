<?php
namespace BeechIt\NewsTtnewsimport\Jobs;

/***************************************************************
*  Copyright notice
*
*  (c) 2010 Georg Ringer <typo3@ringerge.org>
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
use GeorgRinger\News\Domain\Service\NewsImportService;
use GeorgRinger\News\Jobs\AbstractImportJob;

/**
 * Import job
 *
 * @package TYPO3
 * @subpackage news_ttnewsimport
 */
class TTNewsNewsImportJob extends AbstractImportJob {
	/**
	 * @var int
	 */
	#protected $numberOfRecordsPerRun = 30; // Do not limit! Import all if running on console

	protected $importServiceSettings = array(
		'findCategoriesByImportSource' => 'TT_NEWS_CATEGORY_IMPORT'
	);

	/**
	 * Inject import dataprovider service
	 *
	 * @param \BeechIt\NewsTtnewsimport\Service\Import\TTNewsNewsDataProviderService $importDataProviderService
	 * @return void
	 */
	public function injectImportDataProviderService(\BeechIt\NewsTtnewsimport\Service\Import\TTNewsNewsDataProviderService
		$importDataProviderService) {

		$this->importDataProviderService = $importDataProviderService;
	}

	/**
	 * Inject import service
	 *
	 * @param NewsImportService $importService
	 * @return void
	 */
	public function injectImportService(\GeorgRinger\News\Domain\Service\NewsImportService $importService) {
		$this->importService = $importService;
	}
}