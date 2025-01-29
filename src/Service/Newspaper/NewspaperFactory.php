<?php

namespace App\Service\Newspaper;

use App\Exception\NewspaperException;

class NewspaperFactory
{
    const array AVAILABLE_NEWSPAPER_KEYS = [
        ElMundoScrapedNewspaper::NEWSPAPER_KEY,
        ElPaisScrapedNewspaper::NEWSPAPER_KEY
    ];

    public function __construct(
        private readonly ElMundoScrapedNewspaper $elMundoNewspaper,
        private readonly ElPaisScrapedNewspaper  $elPaisNewspaper,
        private readonly AvantioNewspaper        $avantioNewspaper
    )
    {
    }

    /**
     * @throws NewspaperException
     */
    public function create(string $newspaperKey): NewspaperInterface
    {
        return match (true) {
            ElMundoScrapedNewspaper::NEWSPAPER_KEY === $newspaperKey => $this->elMundoNewspaper,
            ElPaisScrapedNewspaper::NEWSPAPER_KEY === $newspaperKey => $this->elPaisNewspaper,
            AvantioNewspaper::NEWSPAPER_KEY === $newspaperKey => $this->avantioNewspaper,
            default => throw new NewspaperException("Unrecognized newspaper key"),
        };
    }
}
