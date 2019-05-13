TYPO3 extension "news_ttnewsimport"
===================================

This extension imports records from `EXT:tt_news` to `EXT:news` with support for multiple 3rd party extensions which enhance tt_news.

Execution via shell is supported and recommend for all important tasks. For EXT:typo3_console use `./typo3cms` instead of `./typo3/cli_dispatch.phpsh extbase`.

**Requirements**

* TYPO3 CMS >= 6.2
    * EXT:typo3db_legacy for TYPO3 CMS >= 9
* Ext:news >= 3.0

**License**

GPL v2


Migrate records
---------------


The records `tt_news` are migrated to `tx_news_domain_model_news` and `tt_news_cat` to `sys_category`.

The following 3rd party extensions are supported during the migration and are not needed anymore:

* EXT:dam: The dam records are migrated using the new FAL API.
* EXT:jg_youtubeinnews: YouTube links are migrated to EXT:news media elements
* EXT:tl_news_linktext: Related links are migrated to ext:news link elements
* EXT:mbl_newsevent are migrated to the available fields of EXT:roq_newsevent (News event extension for EXT:news)
* EXT:fal_ttnews: Existing relations are migrated to EXT:news fal_media elements (EXT:news v7.x)

Usage
^^^^^

Import of tt_news entries can be done manually via TYPO3 backend module of EXT:news or using shell.

**Important**: First start import of categories if any, as they will be considered for news entries.

**A) Import manually via TYPO3 backend module**

This extension registers new import classes for EXT:news and are available in backend modul.

* After installing the extension, switch to the module "**News Import**".
* Select the wizard you need and press *Start*.

**Important:** Reopen the module after importing categories to import news.
If you don't reopen the module, some news can be imported twice.

**B) Import using shell**

.. code-block:: bash

	# Import tt_news category records
	./typo3/cli_dispatch.phpsh extbase ttnewsimport:importttnewscategory

.. code-block:: bash

	# Import tt_news news records
	./typo3/cli_dispatch.phpsh extbase ttnewsimport:importttnewsnews


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

**Hint:** Since TYPO3 version 9.5 command 'run' (which creates a new record below the existing plugin) will not work, if workspace versioned tt_content plugins exists with negative pids. Command 'replace' still works for TYPO3 9.5

.. code-block:: bash

	# Gives you some information about how many plugins are still to be migrated
	./typo3/cli_dispatch.phpsh extbase ttnewspluginmigrate:check

.. code-block:: bash

	# Creates the plugins for *EXT:news* by creating a new record below the plugin of *EXT:tt_news*
	# This makes it possible for you to cross check the migration and adapt the plugins
	./typo3/cli_dispatch.phpsh extbase ttnewspluginmigrate:run

.. code-block:: bash

	# Replace tt_news plugins directly without creating copies
	./typo3/cli_dispatch.phpsh extbase ttnewspluginmigrate:replace

.. code-block:: bash

	# Hide the old tt_news plugins
	./typo3/cli_dispatch.phpsh extbase ttnewspluginmigrate:removeOldPlugins

	# Deletes the old tt_news plugins
	./typo3/cli_dispatch.phpsh extbase ttnewspluginmigrate:removeOldPlugins delete=1
	
Optional
--------

FAL
^^^

If EXT:fal_ttnews was used, there exists two options to migrate images/media.

**A) Migrate manually use of updater by EXT:news_falttnewsimport**

Download EXT:news_falttnewsimport from TER. Run the update script via ExtensionManager and unsinstall afterwards.

**B) Migrate using shell**

The update routine from EXT:news_falttnewsimport is implemented as command and can be executed via shell:

.. code-block:: bash

	# Migrate fal_ttnews entries
	./typo3/cli_dispatch.phpsh extbase falttnewsmigration:migratefalttnews

RealUrl & Routing
^^^^^^^^^^^^^^^^^

In case of using EXT:realurl (TYPO3 v8.7 and below) aliases for news with similar titles should be keept. EXT:news documentation offers a SQL update to do so, which is implemented here and can be executed via shell:

.. code-block:: bash

	# Migrate tt_news realurl unique alias
	./typo3/cli_dispatch.phpsh extbase realurluniquealiasmigration:migratettnewsrealurluniquealias

**HINT:** The script supports EXT:realurl v2.x For versions below 2.x see class `RealUrlUniqueAliasMigrationCommandController` and switch the uncomment lines in `migrateTtNewsRealurlUniqueAliasCommand` method.

EXT:news v7 introduces news `path_segment` for URL generation, which is compatible with EXT:realurl until TYPO3 v8.7.
TYPO3 v9.5 introduced Routing, where news `path_segment` is recommend to use for URL generation.
To keep aliases as created by EXT:realurl they should migrated to news `path_segment`:

.. code-block:: bash

	# Migrate realurl unique alias into news.path_segment
	./typo3/cli_dispatch.phpsh extbase realurluniquealiasmigration:migraterealurluniquealiasintopathsegment

This will only work for empty news `path_segment`!

The result can still lead in empty slugs fields, which can be manually updated via installtool upgrade wizard "Updates slug field 'path_segment' of EXT:news records" or using EXT:typo3-console:

.. code-block:: bash

	# Run upgrade wizard:newsSlug
	./typo3cms upgrade:wizard newsSlug

Known issues
------------
see FAQ Section in
Documentation/Misc/Index.rst

