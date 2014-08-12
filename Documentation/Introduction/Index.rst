.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


What does it do?
================

This extension (news_ttnewsimport) makes it possible to import tt_news into news.

Does not require to have tt_news, dam and/or dam connectors extensions to be installed. Just keep the database tables of these extensions until you imported all.

Features
--------

- DAM support, images and files will be converted to ext:news (FAL)media elements
- Category mountpoints in be_groups and be_users will be adjusted to sys_category
- Converts jg_youtubeinnews YouTube links to ext:news media elements
- Converts tl_news_linktext related links to ext:news link elements
- Imports data from EXT:mbl_newsevent to the available fields of EXT:roq_newsevent (News event extension for EXT:news)

Requirements
------------

- TYPO3 CMS >= 6.2
- Ext:News >= 3.0