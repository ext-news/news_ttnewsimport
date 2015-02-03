<?php
namespace BeechIt\NewsTtnewsimport\Service\Import;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Nikolas Hagelstein <nikolas.hagelstein@gmail.com>
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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tt_news ImportService
 *
 * @package TYPO3
 * @subpackage news_ttnewsimport
 */
class TTNewsNewsDataProviderService implements \Tx_News_Service_Import_DataProviderServiceInterface, \TYPO3\CMS\Core\SingletonInterface  {

	protected $importSource = 'TT_NEWS_IMPORT';

	/**
	 * Get total record count
	 *
	 * @return integer
	 */
	public function getTotalRecordCount() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)',
			'tt_news',
			'deleted=0 AND t3ver_oid = 0 AND t3ver_wsid = 0'
		);

		list($count) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return (int)$count;
	}

	/**
	 * Get the partial import data, based on offset + limit
	 *
	 * @param integer $offset offset
	 * @param integer $limit limit
	 * @return array
	 */
	public function getImportData($offset = 0, $limit = 50) {
		$importData = array();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
			'tt_news',
			'deleted=0 AND t3ver_oid = 0 AND t3ver_wsid = 0',
			'',
			'sys_language_uid ASC',
			$offset . ',' . $limit
		);

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

			$importData[] = array(
				'pid' => $row['pid'],
				'hidden' => $row['hidden'],
				'tstamp' => $row['tstamp'],
				'crdate' => $row['crdate'],
				'cruser_id' => $row['cruser_id'],
				'l10n_parent' => $row['l18n_parent'],
				'sys_language_uid' => $row['sys_language_uid'],
				'starttime' => $row['starttime'],
				'endtime'  => $row['endtime'],
				'fe_group'  => $row['fe_group'],
				'title' => $row['title'],
				'teaser' => $row['short'],
				'bodytext' => str_replace('###YOUTUBEVIDEO###', '', $row['bodytext']),
				'datetime' => $row['datetime'],
				'archive' => $row['archivedate'],
				'author' => $row['author'],
				'author_email' => $row['author_email'],
				'type' => $row['type'],
				'keywords' => $row['keywords'],
				'externalurl' => $row['ext_url'],
				'internalurl' => $row['page'],
				'categories' => $this->getCategories($row['uid']),
				'media' => $this->getMedia($row),
				'related_files' => $this->getFiles($row),
				'related_links' => array_key_exists('tx_tlnewslinktext_linktext', $row) ? $this->getRelatedLinksTlNewsLinktext($row['links'], $row['tx_tlnewslinktext_linktext']) : $this->getRelatedLinks($row['links']),
				'content_elements' => $row['tx_rgnewsce_ce'],
				'import_id' => $row['uid'],
				'import_source' => $this->importSource
			);
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return $importData;
	}

	/**
	 * Parses the related files
	 *
	 * @param array $row
	 * @return array
	 */
	protected function getFiles(array $row) {
		$relatedFiles = array();

		// tx_damnews_dam_media
		if (!empty($row['tx_damnews_dam_media'])) {

			// get DAM items
			$files = $this->getDamItems($row['uid'], 'tx_damnews_dam_media');
			foreach ($files as $damUid => $file) {
				$relatedFiles[] = array(
					'file' => $file
				);
			}
		}

		if (!empty($row['news_files'])) {
			$files = GeneralUtility::trimExplode(',', $row['news_files']);

			foreach ($files as $file) {
				$relatedFiles[] = array(
					'file' => 'uploads/media/' . $file
				);
			}
		}

		return $relatedFiles;
	}

	/**
	 * Get correct categories of a news record
	 *
	 * @param integer $newsUid news uid
	 * @return array
	 */
	protected function getCategories($newsUid) {
		$categories = array();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
			'tt_news_cat_mm',
			'uid_local=' . $newsUid);

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$categories[] = $row['uid_foreign'];
		}

		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return $categories;
	}

	/**
	 * Get correct media elements to be imported
	 *
	 * @param array $row news record
	 * @return array
	 */
	protected function getMedia(array $row) {
		$media = array();
		$count = 0;

		// tx_damnews_dam_images
		if (!empty($row['tx_damnews_dam_images'])) {

			// get DAM data
			$files = $this->getDamItems($row['uid'], 'tx_damnews_dam_images');

			$captions = GeneralUtility::trimExplode(chr(10), $row['imagecaption'], FALSE);
			$alts = GeneralUtility::trimExplode(chr(10), $row['imagealttext'], FALSE);
			$titles = GeneralUtility::trimExplode(chr(10), $row['imagetitletext'], FALSE);

			foreach ($files as $damUid => $file) {
				$media[] = array(
					'title' => $titles[$count],
					'alt' => $alts[$count],
					'caption' => $captions[$count],
					'image' => $file,
					'showinpreview' => (int)$count == 0
				);
				$count++;
			}
		}

		if (!empty($row['image'])) {
			$images = GeneralUtility::trimExplode(',', $row['image'], TRUE);
			$captions = GeneralUtility::trimExplode(chr(10), $row['imagecaption'], FALSE);
			$alts = GeneralUtility::trimExplode(chr(10), $row['imagealttext'], FALSE);
			$titles = GeneralUtility::trimExplode(chr(10), $row['imagetitletext'], FALSE);

			$i = 0;
			foreach ($images as $image) {
				$media[] = array(
					'title' => $titles[$i],
					'alt' => $alts[$i],
					'caption' => $captions[$i],
					'image' => 'uploads/pics/' . $image,
					'type' => 0,
					'showinpreview' => (int)$count == 0
				);
				$i ++;
				$count ++;
			}
		}

		$media = array_merge($media, $this->getMultimediaItems($row));

		return $media;
	}

	/**
	 * Get link elements to be imported
	 *
	 * @param string $newsLinks
	 * @return array
	 */
	protected function getRelatedLinks($newsLinks) {
		$links = array();

		if (empty($newsLinks)) {
			return $links;
		}

		$newsLinks = str_replace(array('<link ', '</link>'), array('<LINK ', '</LINK>'), $newsLinks);

		$linkList = GeneralUtility::trimExplode('</LINK>', $newsLinks, TRUE);
		foreach ($linkList as $singleLink) {
			if (strpos($singleLink, '<LINK') === FALSE) {
				continue;
			}
			$title = substr(strrchr($singleLink, '>'), 1);
			$uri = str_replace('>' . $title, '', substr(strrchr($singleLink, '<link '), 6));
			$links[] = array(
				'uri' => $uri,
				'title' => $title,
				'description' => '',
			);
		}
		return $links;
	}

	/**
	 * Get link elements to be imported when using EXT:tl_news_linktext
	 * This extension adds an additional field for link texts that are separated by a line break
	 *
	 * @param string $newsLinks
	 * @param string $newsLinksTexts
	 * @return array
	 */
	protected function getRelatedLinksTlNewsLinktext($newsLinks, $newsLinksTexts) {
		$links = array();

		if (empty($newsLinks)) {
			return $links;
		}

		$newsLinks = str_replace("\r\n", "\n", $newsLinks);
		$newsLinksTexts = str_replace("\r\n", "\n", $newsLinksTexts);

		$linkList = GeneralUtility::trimExplode("\n", $newsLinks, TRUE);
		$linkTextList = GeneralUtility::trimExplode("\n", $newsLinksTexts, TRUE);

		$iterator = 0;
		foreach ($linkList as $uri) {
			$links[] = array(
				'uri' => $uri,
				'title' => array_key_exists($iterator, $linkTextList) ? $linkTextList[$iterator] : $uri,
				'description' => '',
			);
			$iterator++;
		}
		return $links;
	}

	/**
	 * Get DAM file names
	 *
	 * @param $newsUid
	 * @param $field
	 * @return array
	 */
	protected function getDamItems($newsUid, $field) {

		$files = array();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_dam.uid, tx_dam.file_name, tx_dam.file_path',
			'tx_dam', 'tx_dam_mm_ref', 'tt_news',
			'AND tx_dam_mm_ref.tablenames="tt_news" AND tx_dam_mm_ref.ident="'.$field.'" ' .
			'AND tx_dam_mm_ref.uid_foreign="' . $newsUid . '"', '', 'tx_dam_mm_ref.sorting_foreign ASC');

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$files[$row['uid']] = $row['file_path'].$row['file_name'];
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return $files;
	}

	/**
	 * Parse row for custom plugin info
	 *
	 * @param $row current row
	 * @return array
	 */
	protected function getMultimediaItems($row) {

		$media = array();

		/**
		 * Ext:jg_youtubeinnews
		 */
		if (!empty($row['tx_jgyoutubeinnews_embed'])) {
			if (preg_match_all('#((http|https)://)?([a-zA-Z0-9\-]*\.)+youtube([a-zA-Z0-9\-]*\.)+[a-zA-Z0-9]{2,4}(/[a-zA-Z0-9=.?&_-]*)*#i', $row['tx_jgyoutubeinnews_embed'], $matches)) {
				$matches = array_unique($matches[0]);
				foreach ($matches as $url) {
					$urlInfo = parse_url($url);
					$media[] = array(
						'type' => \Tx_News_Domain_Model_Media::MEDIA_TYPE_MULTIMEDIA,
						'multimedia' => $url,
						'title' => $urlInfo['host'],
					);
				}
			}
		}

		return $media;
	}
}