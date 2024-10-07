<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\Xxllnc;

use OWC\Zaaksysteem\Contracts\AbstractClient;
use OWC\Zaaksysteem\Endpoints\CatalogussenEndpoint;
use OWC\Zaaksysteem\Endpoints\EigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoints\EnkelvoudiginformatieobjectenEndpoint;
use OWC\Zaaksysteem\Endpoints\InformatieobjecttypenEndpoint;
use OWC\Zaaksysteem\Endpoints\ObjectinformatieEndpoint;
use OWC\Zaaksysteem\Endpoints\ResultaattypenEndpoint;
use OWC\Zaaksysteem\Endpoints\ResultatenEndpoint;
use OWC\Zaaksysteem\Endpoints\RollenEndpoint;
use OWC\Zaaksysteem\Endpoints\RoltypenEndpoint;
use OWC\Zaaksysteem\Endpoints\StatussenEndpoint;
use OWC\Zaaksysteem\Endpoints\StatustypenEndpoint;
use OWC\Zaaksysteem\Endpoints\TakenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZaakeigenschappenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZaakinformatieobjectenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZaakobjectenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZaaktypenEndpoint;
use OWC\Zaaksysteem\Endpoints\ZakenEndpoint;

class Client extends AbstractClient
{
    public const CLIENT_NAME = 'xxllnc';
    public const CALLABLE_NAME = 'xxllnc.client';

    public const AVAILABLE_ENDPOINTS = [
        // Zaken API
        'zaken' => [ZakenEndpoint::class, 'zaken'],
        'statussen' => [StatussenEndpoint::class, 'zaken'],
        'rollen' => [RollenEndpoint::class, 'zaken'],
        'resultaten' => [ResultatenEndpoint::class, 'zaken'],
        'zaakeigenschappen' => [ZaakeigenschappenEndpoint::class, 'zaken'],
        'zaakinformatieobjecten' => [ZaakinformatieobjectenEndpoint::class, 'zaken'],
        'zaakobjecten' => [ZaakobjectenEndpoint::class, 'zaken'],

        /**
         * Not yet implemented
         */
        // 'zaakcontactmomenten' => Endpoint::class,
        // 'zaakverzoeken' => Endpoint::class,

        // Catalogi API
        'zaaktypen' => [ZaaktypenEndpoint::class, 'catalogi'],
        'statustypen' => [StatustypenEndpoint::class, 'catalogi'],
        'roltypen' => [RoltypenEndpoint::class, 'catalogi'],
        'catalogussen' => [CatalogussenEndpoint::class, 'catalogi'],
        'resultaattypen' => [ResultaattypenEndpoint::class, 'catalogi'],
        'informatieobjecttypen' => [InformatieobjecttypenEndpoint::class, 'catalogi'],
        'eigenschappen' => [EigenschappenEndpoint::class, 'catalogi'],

        /**
         * Not yet implemented
         */
        // 'besluittypen' => Endpoint::class,
        // 'zaaktype-informatieobjecttypen' => Endpoint::class,

        // Documenten API
        'objectinformatieobjecten' => [ObjectinformatieEndpoint::class, 'documenten'],
        'enkelvoudiginformatieobjecten' => [EnkelvoudiginformatieobjectenEndpoint::class, 'documenten'],
        /**
         * Not yet implemented
         */
        // 'gebruiksrechten' => Endpoint::class,
        // 'objectinformatieobjecten' => Endpoint::class,
        // 'bestandsdelen' => Endpoint::class,

        // Mijn Taken API
        'taken' => [TakenEndpoint::class, 'taken'],
    ];
}
