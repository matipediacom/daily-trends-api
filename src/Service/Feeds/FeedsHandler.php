<?php

namespace App\Service\Feeds;

use App\Exception\FeedsImporterException;
use App\Repository\FeedRepository;
use App\Service\DailyHelper;
use App\Service\Newspaper\NewspaperFactory;
use Doctrine\ODM\MongoDB\MongoDBException;
use Throwable;

class FeedsHandler
{
    private const int REQUIRED_TODAY_FEEDS = 10;

    public function __construct(
        private readonly FeedRepository   $feedRepository,
        private readonly NewspaperFactory $newspaperFactory,
        private readonly FeedsImporter    $feedsImporter,
        private readonly DailyHelper      $dailyHelper
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
        foreach ($newspaperKeys as $newspaperKey) {
            if (!in_array($newspaperKey, NewspaperFactory::AVAILABLE_NEWSPAPER_KEYS)) {
                continue;
            }

            $newspaper = $this->newspaperFactory->create($newspaperKey);

            $this->feedsImporter->import($newspaper);
        }

        return $this->feedRepository->findNewspapersFeedsInRange(
            newspaperKeys: $newspaperKeys,
            start: $this->dailyHelper->today(),
            end: $this->dailyHelper->tomorrow(),
            limit: self::REQUIRED_TODAY_FEEDS,
        );
    }
}
