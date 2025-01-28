<?php

namespace App\Repository;

use App\Document\Feed;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Throwable;

readonly class FeedRepository
{
    public function __construct(
        private DocumentManager $documentManager
    )
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

    public function findFeedsInRange(DateTime $start, DateTime $end, ?int $limit = null): array
    {
        return $this->documentManager->getRepository(Feed::class)->findBy([
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
