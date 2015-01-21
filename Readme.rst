TYPO3 extension "news_ttnewsimport"
===================================

EXT:News tt\_news importer.

This is an extraction of the tt_news importer code original from `EXT:News` enhanced with `EXT:DAM` and `EXT:jg_youtubeinnews` support.

**Features**

- DAM support
- Converts jg_youtubeinnews YouTube links to ext:news media elements
- Converts tl_news_linktext related links to ext:news link elements
- Imports data from EXT:mbl_newsevent to the available fields of EXT:roq_newsevent (News event extension for EXT:news)

**Requirements**

- TYPO3 CMS >= 6.2
- Ext:News >= 3.0

# plugin migration

not supported:

orderBy: archivedate ,author,,type,random,


// not migrated
//    [croppingLenghtOptionSplit] =>
//    [firstImageIsPreview] => 0
//    [forceFirstImageIsPreview] => 0
//    [myTS] =>
//    [template_file] =>
//    [altLayoutsOptionSplit] =>
//    [maxWordsInSingleView] =>
//    [catImageMode] =>
//    [catImageMaxWidth] =>
//    [catImageMaxHeight] =>
//    [maxCatImages] =>
//    [catTextMode] =>
//    [maxCatTexts] =>
//    [alternatingLayouts] =>
