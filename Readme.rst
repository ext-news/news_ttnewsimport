TYPO3 extension "news_ttnewsimport"
=====================

EXT:News tt\_news importer.

This is an extraction of the tt_news importer code original from `EXT:News` enhanced with `EXT:DAM` and `EXT:jg_youtubeinnews` support.

**Features**

- DAM support
- Converts jg_youtubeinnews YouTube links to ext:news media elements
- Converts tl_news_linktext related links to ext:news link elements
- Imports data from EXT:mbl_newsevent to the available fields of EXT:roq_newsevent (News event extension for EXT:news)
- Imports start datetime and archive datefrom EXT:mbl_newsevent to to the native fields of EXT:news (datetime and archive). Notes: you must handle the is_archive flag yourself (e.g. by category or pid). And: archive is reduced to a date, as EXT:news doesn't provide time for archiving (makes sense due to caching).

**Requirements**

- TYPO3 CMS >= 6.2
- Ext:News >= 3.0