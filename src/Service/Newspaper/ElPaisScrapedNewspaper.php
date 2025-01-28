<?php

namespace App\Service\Newspaper;

use App\Document\Feed;
use DateTime;
use Throwable;

class ElPaisScrapedNewspaper extends AbstractScrapedNewspaper
{
    const string NEWSPAPER_KEY = 'el_pais';
    private const string NEWSPAPER_NAME = 'El País';

    public function getLastFeeds(): array
    {
        try {
            return $this->getCrawler()->reduce(function ($node) {
                $title = $node->filter('.c_h')->text('');

                if (empty($title)) {
                    return false;
                }

                return true;
            })->each(function ($node) {
                return new Feed()
                    ->setTitle($node->filter('.c_h')->text(''))
                    ->setNewspaperName(self::NEWSPAPER_NAME)
                    ->setPublishedAt(new DateTime)
                    ->setNewspaperKey(self::NEWSPAPER_KEY);
            });

        } catch (Throwable) {
            return [];
        }
    }

    public function getNewspaperKey(): string
    {
        return self::NEWSPAPER_KEY;
    }

    protected function getLastNewsUri(): string
    {
        return 'https://elpais.com/ultimas-noticias/';
    }

    protected function getCssSelector(): string
    {
        return '.c-d';
    }
}
