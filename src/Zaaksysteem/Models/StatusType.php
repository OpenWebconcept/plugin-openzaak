<?php declare(strict_types=1);

namespace OWC\Zaaksysteem\Models;

class StatusType
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getURL(): string
    {
        return $this->data['url'] ?? '';
    }

    public function getDesc(): string
    {
        return $this->data['omschrijving'] ?? '';
    }

    public function setIsPast(bool $value): self
    {
        $this->data['isPast'] = $value;

        return $this;
    }

    public function isPast(): bool
    {
        return $this->data['isPast'] ?? false;
    }

    public function getGenericDesc(): string
    {
        return $this->data['omschrijvingGeneriek'] ?? '';
    }

    public function getText(): string
    {
        return $this->data['statustekst'] ?? '';
    }

    public function getType(): string
    {
        return $this->data['zaaktype'] ?? '';
    }

    public function getNumber(): int
    {
        return $this->data['volgnummer'] ?? '';
    }

    public function isFinished(): bool
    {
        return $this->data['isEindstatus'] ?? '';
    }

    public function shouldInform(): bool
    {
        return $this->data['informeren'] ?? '';
    }
}