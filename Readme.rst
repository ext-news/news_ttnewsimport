TYPO3 extension "news_ttnewsimport"
===================================

This extension imports records from `EXT:tt_news` to `EXT:news` with support for multiple 3rd party extensions which enhance tt_news.

**Requirements**

* TYPO3 CMS >= 6.2
* Ext:news >= 3.0

**License**

GPL v2


Migrate records
---------------


The records `tt_news` are migrated to `tx_news_domain_model_news` and `tt_news_cat` to `sys_category`.

The following 3rd party extensions are supported during the migration and are not needed anymore:

* DAM: The dam records are migrated using the new FAL API.
* jg_youtubeinnews: YouTube links are migrated to EXT:news media elements
* tl_news_linktext: Related links are migrated to ext:news link elements
* EXT:mbl_newsevent are migrated to the available fields of EXT:roq_newsevent (News event extension for EXT:news)

Usage
^^^^^

* After installing the extension, switch to the module "**News Import**".
* Select the wizard you need and press *Start*.


Plugin migration
----------------

You can migrate the plugins of `tt_news` to `news` by using the command line.

Be aware that not all options are migrated. Supported are:

* what_to_display
* listOrderBy (except: archivedate, author, type, random)
* ascDesc
* categoryMode
* categorySelection
* useSubCategories
* archive
* imageMaxWidth
* imageMaxHeight
* listLimit
* noPageBrowser
* croppingLenght
* PIDitemDisplay
* backPid
* pages
* recursive

**not supported:**

* croppingLenghtOptionSplit
* firstImageIsPreview
* forceFirstImageIsPreview
* myTS
* template_file
* altLayoutsOptionSplit
* maxWordsInSingleView
* catImageMode
* catImageMaxWidth
* catImageMaxHeight
* maxCatImages
* catTextMode
* maxCatTexts
* alternatingLayouts

Usage
^^^^^

**Important:** Run the plugin migration **after** the record migration!

.. code-block:: bash

	# Gives you some information about how many plugins are still to be migrated
	./typo3/cli_dispatch.phpsh extbase ttnewspluginmigrate:check

.. code-block:: bash

	# Creates the plugins for *EXT:news* by creating a new record below the plugin of *EXT:tt_news*.
	# This makes it possible for you to cross check the migration and adapt the plugins.
	./typo3/cli_dispatch.phpsh extbase ttnewspluginmigrate:run

.. code-block:: bash

	# Hide the old tt_news plugins.
	./typo3/cli_dispatch.phpsh extbase ttnewspluginmigrate:removeOldPlugins

	# Deletes the old tt_news plugins.
	./typo3/cli_dispatch.phpsh extbase ttnewspluginmigrate:removeOldPlugins delete=1

