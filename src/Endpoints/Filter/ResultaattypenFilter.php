<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

use OWC\Zaaksysteem\Entities\Zaaktype;

class ResultaattypenFilter extends AbstractFilter
{
    public function byZaaktype(Zaaktype $zaaktype)
    {
        return $this->add('zaaktype', $zaaktype->uuid);
    }

    public function byStatus(string $status)
    {
        if (! in_array($status, ['alles', 'definitief', 'concept'])) {
            throw new \InvalidArgumentException("Unknown statustype status {$status}");
        }

        return $this->add('status', $status);
    }

    public function byStatusConcept()
    {
        return $this->add('status', 'concept');
    }

    public function byStatusDefinitief()
    {
        return $this->add('status', 'definitief');
    }

    public function byStatusAlles()
    {
        return $this->add('status', 'alles');
    }

    public function byStatusMijnOmgeving()
    {
        return $this->add('status', 'mijnomgeving');
    }
}
