<?php

namespace App\Service\Newspaper;

class NewspaperFactory
{
    const array AVAILABLE_NEWSPAPER_KEYS = [
        ElMundoNewspaper::NEWSPAPER_KEY,
        ElPaisNewspaper::NEWSPAPER_KEY
    ];

    public function __construct(
        private readonly ElMundoNewspaper $elMundoNewspaper,
        private readonly ElPaisNewspaper  $elPaisNewspaper
    )
    {
    }

    public function create(string $newspaperKey): NewspaperInterface
    {
        return match (true) {
            ElMundoNewspaper::NEWSPAPER_KEY === $newspaperKey => $this->elMundoNewspaper,
            ElPaisNewspaper::NEWSPAPER_KEY === $newspaperKey => $this->elPaisNewspaper,
            default => "Unrecognized newspaper key",
        };
    }
}
