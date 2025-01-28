<?php

namespace App\Service\Newspaper;

class NewspaperFactory
{
    const string EL_MUNDO_NEWSPAPER_KEY = 'el_mundo';
    const string EL_PAIS_NEWSPAPER_KEY = 'el_pais';

    const array AVAILABLE_NEWSPAPER_KEYS = [
        self::EL_MUNDO_NEWSPAPER_KEY,
    ];

    public function __construct(
        private readonly ElMundoNewspaper $elMundoNewspaper
    )
    {
    }

    public function create(string $newspaperKey): NewspaperInterface
    {
        return match (true) {
            'el_mundo' === $newspaperKey => $this->elMundoNewspaper,
            default => "Unrecognized newspaper key",
        };
    }
}
