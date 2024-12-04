<?php

namespace OWC\Zaaksysteem\Traits;

trait ZaakIdentification
{
    /**
     * Converts slashes ("/") in a 'zaak' identification to double dashes ("--").
     * This ensures compatibility with the routing system, which does not support slashes in identifiers.
     */
    public function encodeZaakIdentification(string $identification): string
    {
        return str_replace('/', '--', $identification);
    }

    /**
     * Restores the original 'zaak' identification by replacing double dashes ("--") with slashes ("/").
     * This is used to decode the 'zaak' identification back to its original form.
     */
    public function decodeZaakIdentification(string $identification): string
    {
        return str_replace('--', '/', $identification);
    }
}
