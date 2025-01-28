<?php

namespace App\Service\Newspaper;

use App\Repository\FeedRepository;
use App\Service\DailyHelper;

class AvantioNewspaper implements NewspaperInterface
{
    const string NEWSPAPER_KEY = 'avantio';
    private const string NEWSPAPER_NAME = 'Avantio News';

    public function __construct(private readonly FeedRepository $feedRepository, private readonly DailyHelper $dailyHelper)
    {
    }

    public function getLastFeeds(): array
    {
        return $this->feedRepository->findNewspaperFeedsInRange(
            newspaperKey: self::NEWSPAPER_KEY,
            start: $this->dailyHelper->today(),
            end: $this->dailyHelper->tomorrow()
        );
    }

    public function getNewspaperKey(): string
    {
        return self::NEWSPAPER_KEY;
    }
}
