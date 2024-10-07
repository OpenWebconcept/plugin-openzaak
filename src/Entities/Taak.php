<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities;

class Taak extends Entity
{
    protected array $casts = [
        'zaak' => Casts\Lazy\Zaak::class,
    ];

    public function title(): string
    {
        return $this->getValue('title', '');
    }

    public function identification(): string
    {
        return $this->getValue('identificatie', '');
    }

    public function clarification(): string
    {
        return $this->getValue('toelichting', '');
    }
}
