<?php

namespace App\Service\Newspaper;

use App\Service\WebScraper;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractScrapedNewspaper implements NewspaperInterface
{
    public function __construct(protected WebScraper $webScraper)
    {
    }

    protected function getCrawler(): Crawler
    {
        return $this->webScraper->scrape($this->getLastNewsUri(), $this->getCssSelector());
    }

    abstract protected function getCssSelector(): string;

    abstract protected function getLastNewsUri(): string;
}
