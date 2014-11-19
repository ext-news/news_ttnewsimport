TYPO3 extension "news_ttnewsimport"
=====================

EXT:News tt\_news importer.

This is an extraction of the tt_news importer code original from `EXT:News` enhanced with `EXT:DAM`, `EXT:jg_youtubeinnews` and `EXT:mbl_newsevent` support.

**Features**

- DAM support
- Converts jg_youtubeinnews YouTube links to ext:news media elements
- Converts tl_news_linktext related links to ext:news link elements
- Imports data from EXT:mbl_newsevent to the available fields of EXT:roq_newsevent (News event extension for EXT:news)
- Imports main data (tx_mblnewsevent_from, tx_mblnewsevent_fromtime, tx_mblnewsevent_to) from EXT:mbl_newsevent to to the native fields of EXT:news (datetime and archive). Notes: you must handle the `is_archive` flag yourself (e.g. by category or pid). Also, tx_mblnewsevent_totime is omitted, as the end time is now only used as an archive date.

**Requirements**

- TYPO3 CMS >= 6.2
- Ext:News >= 3.0