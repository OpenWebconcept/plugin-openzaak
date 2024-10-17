<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Routing;

use OWC\Zaaksysteem\Foundation\ServiceProvider;
use OWC\Zaaksysteem\Routing\Controllers\SingleTaakRoutingController;
use OWC\Zaaksysteem\Routing\Controllers\SingleZaakRoutingController;
use OWC\Zaaksysteem\Routing\Controllers\ZaakInformationObjectRoutingController;

class RoutingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        (new SingleTaakRoutingController())->register();
        (new SingleZaakRoutingController())->register();
        (new ZaakInformationObjectRoutingController())->register();
    }
}
