<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

use DateTimeInterface;
use OWC\Zaaksysteem\Entities\Zaak;

class TakenFilter extends AbstractFilter
{
    // identificatie
    // bronorganisatie
    // archiefnominatie
    // archiefnominatie__in
    // archiefactiedatum
    // archiefactiedatum__lt
    // archiefactiedatum__gt
    // archiefstatus
    // archiefstatus__in
    // rol__betrokkeneType
    // rol__betrokkene
    // rol__omschrijvingGeneriek
    // maximaleVertrouwelijkheidaanduiding
    // rol__betrokkeneIdentificatie__natuurlijkPersoon__inpBsn
    // rol__betrokkeneIdentificatie__medewerker__identificatie
    // rol__betrokkeneIdentificatie__organisatorischeEenheid__identificatie
    // ordering

    public function byZaak(Zaak $zaak)
    {
        return $this->add('zaak', $zaak->url);
    }

    /**
     * Temp: this should be removed when the mijn-taken api is properly configured.
     *
     * @param [type] $zaak
     * @return void
     */
    public function byZaakURL($zaak)
    {
        return $this->add('zaak', $zaak);
    }
}
