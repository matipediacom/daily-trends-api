<?php

namespace App\Service;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class WebScraper
{
    public function scrape(string $uri, string $cssSelectorName): Crawler
    {
        $browser = new HttpBrowser(HttpClient::create());

        $crawler = $browser->request('GET', $uri);

        return $crawler->filter($cssSelectorName);
    }
}
