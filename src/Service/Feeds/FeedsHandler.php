<?php

namespace App\Service\Feeds;

use App\Exception\FeedsImporterException;
use App\Repository\FeedRepository;
use App\Service\DailyHelper;
use App\Service\Newspaper\NewspaperFactory;
use Doctrine\ODM\MongoDB\MongoDBException;
use Throwable;

readonly class FeedsHandler
{
    public function __construct(
        private FeedRepository   $feedRepository,
        private NewspaperFactory $newspaperFactory,
        private FeedsImporter    $feedsImporter,
        private DailyHelper      $dailyHelper
    )
    {
    }

    /**
     * @throws FeedsImporterException
     * @throws Throwable
     * @throws MongoDBException
     */
    public function getTrendingFeeds(array $newspaperKeys): array
    {
        $dailyFeeds = [];
        foreach ($newspaperKeys as $newspaperKey) {
            if (!in_array($newspaperKey, NewspaperFactory::AVAILABLE_NEWSPAPER_KEYS)) {
                continue;
            }

            $newspaper = $this->newspaperFactory->create($newspaperKey);

            $this->feedsImporter->import($newspaper);

            $dailyFeeds = array_merge(
                $dailyFeeds,
                $this->feedRepository->findFeedsInRange(
                    start: $this->dailyHelper->today(),
                    end: $this->dailyHelper->tomorrow(),
                    limit: FeedsImporter::REQUIRED_TODAY_FEEDS,
                ));
        }

        return $dailyFeeds;

    }
}
