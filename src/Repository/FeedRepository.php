<?php

namespace App\Repository;

use App\Document\Feed;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Throwable;

class FeedRepository
{
    public function __construct(private readonly DocumentManager $documentManager)
    {
    }

    /**
     * @throws Throwable
     * @throws MongoDBException
     */
    public function save(Feed $feed): void
    {
        $this->documentManager->persist($feed);
        $this->documentManager->flush();
    }

    public function findNewspapersFeedsInRange(array $newspaperKeys, DateTime $start, DateTime $end, ?int $limit = null): array
    {
        return $this->documentManager->getRepository(Feed::class)->findBy([
            'newspaperKey' => [
                '$in' => $newspaperKeys,
            ],
            'publishedAt' => [
                '$gte' => $start,
                '$lt' => $end,
            ],
        ], null, $limit);
    }

    public function findNewspaperFeedsInRange(string $newspaperKey, DateTime $start, DateTime $end): array
    {
        return $this->documentManager->getRepository(Feed::class)->findBy([
            'newspaperKey' => $newspaperKey,
            'publishedAt' => [
                '$gte' => $start,
                '$lt' => $end,
            ],
        ]);
    }
}
