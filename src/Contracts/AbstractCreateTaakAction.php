<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Traits\FormSetting;

abstract class AbstractCreateTaakAction
{
    use FormSetting;

    public const CLIENT_NAME = '';
    public const CALLABLE_NAME = '';
    public const CLIENT_CATALOGI_URL = '';
    public const CLIENT_ZAKEN_URL = '';
    public const FORM_SETTING_SUPPLIER_KEY = '';

    protected function getApiClient(): Client
    {
        return ContainerResolver::make()->getApiClient(static::CLIENT_NAME);
    }

    protected function requestArgs($taakUUID, $taakTitle, $status): array
    {
        return [
            'id' => $taakUUID,
            'title' => $taakTitle,
            'status' => $status,
        ];
    }

    abstract public function updateTaak($taakUUID, $taakTitle, $status);
}
