<?php

namespace App\Service\Newspaper;

use App\Document\Feed;

interface NewspaperInterface
{
    /** @return Feed[] */
    public function getLastFeeds(): array;

    public function getNewspaperKey(): string;
}
