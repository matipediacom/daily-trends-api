<?php

namespace App\Service\Feeds;

use App\Document\Feed;
use App\Exception\FeedsImporterException;
use App\Repository\FeedRepository;
use App\Service\DailyHelper;
use App\Service\Newspaper\NewspaperInterface;
use Doctrine\ODM\MongoDB\MongoDBException;
use Throwable;

readonly class FeedsImporter
{
    const int REQUIRED_TODAY_FEEDS = 5;

    public function __construct(private FeedRepository $feedRepository, private DailyHelper $dailyHelper)
    {
    }

    /**
     * @throws FeedsImporterException
     * @throws Throwable
     * @throws MongoDBException
     */
    public function import(NewspaperInterface $newspaper): void
    {
        foreach ($newspaper->getLastFeeds() as $feed) {
            if (!$feed instanceof Feed) {
                throw new FeedsImporterException("Invalid Feed");
            }

            if ($this->hasTodayFeeds($feed->getNewspaperKey())) {
                continue;
            }

            $this->feedRepository->save($feed);
        }
    }

    private function hasTodayFeeds(string $newspaperKey): bool
    {
        $todayFeeds = $this->feedRepository
            ->findNewspaperFeedsInRange($newspaperKey, $this->dailyHelper->today(), $this->dailyHelper->tomorrow());

        return count($todayFeeds) >= self::REQUIRED_TODAY_FEEDS;
    }
}
