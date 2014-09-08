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

/**
 * Import job
 *
 * @package TYPO3
 * @subpackage news_ttnewsimport
 */
class TTNewsCategoryImportJob extends \Tx_News_Jobs_AbstractImportJob {

	/**
	 * Inject import dataprovider service
	 *
	 * @param \BeechIt\NewsTtnewsimport\Service\Import\TTNewsCategoryDataProviderService $importDataProviderService
	 * @return void
	 */
	public function injectImportDataProviderService(\BeechIt\NewsTtnewsimport\Service\Import\TTNewsCategoryDataProviderService
		$importDataProviderService) {

		$this->importDataProviderService = $importDataProviderService;
	}

	/**
	 * Inject import service
	 *
	 * @param \BeechIt\NewsTtnewsimport\Service\Import\TTNewsCategoryImportService $importService
	 * @return void
	 */
	public function injectImportService(\BeechIt\NewsTtnewsimport\Service\Import\TTNewsCategoryImportService $importService) {
		$this->importService = $importService;
	}
}