<?php

namespace App\Service\Newspaper;

use App\Document\Feed;
use DateTime;
use Throwable;

class ElMundoScrapedNewspaper extends AbstractScrapedNewspaper
{
    const string NEWSPAPER_KEY = 'el_mundo';
    private const string NEWSPAPER_NAME = 'El Mundo';

    public function getLastFeeds(): array
    {
        try {
            return $this->getCrawler()->reduce(function ($node) {
                $title = $node->filter('.ue-c-cover-content__headline')->text('');

                if (empty($title)) {
                    return false;
                }

                return true;
            })->each(function ($node) {
                return new Feed()
                    ->setTitle($node->filter('.ue-c-cover-content__headline')->text(''))
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
        return 'https://www.elmundo.es/ultimas-noticias.html';
    }

    protected function getCssSelector(): string
    {
        return '.ue-c-cover-content__main';
    }
}
