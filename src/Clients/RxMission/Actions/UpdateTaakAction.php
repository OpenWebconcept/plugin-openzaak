<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\RxMission\Actions;

use Exception;
use OWC\Zaaksysteem\Contracts\AbstractCreateTaakAction;
use OWC\Zaaksysteem\Entities\Taak;

class UpdateTaakAction extends AbstractCreateTaakAction
{
    public const CLIENT_NAME = 'rx-mission';
    public const CALLABLE_NAME = 'rx.client';
    public const CLIENT_CATALOGI_URL = 'rx.catalogi_uri';
    public const CLIENT_ZAKEN_URL = 'rx.zaken_uri';
    public const FORM_SETTING_SUPPLIER_KEY = 'rx-mission';

    public function updateTaak($taakUUID, $taakTitle, $status): Taak
    {
        if (empty($taakUUID)) {
            throw new Exception('Taak UUID onbekend.');
        }

        $client = $this->getApiClient();

        $args = $this->requestArgs($taakUUID, $taakTitle, $status);

        return $client->taken()->update($taakUUID, $args);
    }
}
