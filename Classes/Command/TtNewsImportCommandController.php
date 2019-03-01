<?php
namespace BeechIt\NewsTtnewsimport\Command;

use GeorgRinger\News\Jobs\ImportJobInterface;
use GeorgRinger\News\Utility\ImportJob;

/**
 * Controller to import news from tt_news
 *
 * @author J.Kummer
 */
class TtNewsImportCommandController extends \GeorgRinger\News\Command\NewsImportCommandController
{

    /**
     * Import tt_news category records
     *
     * @cli
     */
    public function importTtNewsCategoryCommand()
    {
        $job = $this->objectManager->get(\BeechIt\NewsTtnewsimport\Jobs\TTNewsCategoryImportJob::class);
        $job->run(0);
    }

    /**
     * Import tt_news news records
     *
     * @cli
     */
    public function importTtNewsNewsCommand()
    {
        $job = $this->objectManager->get(\BeechIt\NewsTtnewsimport\Jobs\TTNewsNewsImportJob::class);
        $job->run(0);
    }

    /**
     * Import tt_news related news records
     *
     * @cli
     */
    public function importTtNewsRelatedNewsCommand()
    {
        $job = $this->objectManager->get(\BeechIt\NewsTtnewsimport\Jobs\TTNewsRelatedNewsImportJob::class);
        $job->run(0);
    }
}
