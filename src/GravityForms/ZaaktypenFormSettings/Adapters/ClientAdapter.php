<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Adapters;

use Closure;
use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Entities\Informatieobjecttype;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\ClientInterface;
use OWC\Zaaksysteem\Support\Collection;

class ClientAdapter implements ClientInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function informatieobjecttypen(): array
    {
        try {
            return $this->getTypes(
                sprintf('%s-form-settings-information-object-type', $this->getClientNamePretty()), // Unique transient key.
                'informatieobjecttypen',
                function (Informatieobjecttype $objecttype) {
                    return [
                        'name' => $objecttype->url,
                        'label' => "{$objecttype->omschrijving} ({$objecttype->vertrouwelijkheidaanduiding})",
                        'value' => $objecttype->url,
                    ];
                },
                'No information object typen found.'
            );
        } catch (Exception $e) {
            return $this->handleNoChoices('informatieobjecttypen');
        }
    }

    public function zaaktypen(): array
    {
        try {
            return $this->getTypes(
                sprintf('%s-form-settings-zaaktypen', $this->getClientNamePretty()), // Unique transient key.
                'zaaktypen',
                function (Zaaktype $zaaktype) {
                    return [
                        'name' => $zaaktype->identificatie,
                        'label' => "{$zaaktype->omschrijving} ({$zaaktype->identificatie})",
                        'value' => $zaaktype->url, // -> when the api supports filtering on zaaktype identification this line should be updated to $zaaktype->identificatie.
                    ];
                },
                'No zaaktypen found.'
            );
        } catch (Exception $e) {
            return $this->handleNoChoices('zaaktypen');
        }
    }

    public function getClientNamePretty(): string
    {
        return $this->client->getClientNamePretty();
    }

    protected function getTypes(string $transientKey, string $endpoint, Closure $prepareCallback, string $emptyMessage): array
    {
        $types = get_transient($transientKey);

        if (is_array($types) && $types) {
            return $types;
        }

        $types = $this->fetchTypes($emptyMessage, $endpoint);
        $types = $this->prepareTypes($types, $prepareCallback);

        if (empty($types)) {
            return [];
        }

        set_transient($transientKey, $types, 64800); // 18 hours.

        return $types;
    }

    protected function fetchTypes(string $emptyMessage, string $endpoint): array
    {
        $page = 1;
        $types = [];
        $requestException = '';

        while ($page) {
            try {
                if ($this->getClientNamePretty() === 'decos-join') {
                    $result = $this->client->$endpoint()->all((new ResultaattypenFilter())->byStatusMijnOmgeving()->page($page));
                } else {
                    $result = $this->client->$endpoint()->all((new ResultaattypenFilter())->byStatusDefinitief()->page($page));
                }
            } catch (Exception $e) {
                $requestException = $e->getMessage();

                break;
            }

            $types = array_merge($types, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        $this->handleEmptyResult($types, $emptyMessage, $requestException);

        return $types;
    }

    protected function prepareTypes(array $types, Closure $prepareCallback): array
    {
        return (array) Collection::collect($types)->map($prepareCallback)->all();
    }

    protected function handleEmptyResult(array $types, string $emptyMessage, string $requestException): void
    {
        if (empty($types)) {
            $exceptionMessage = $emptyMessage;

            if (! empty($requestException)) {
                $exceptionMessage = sprintf('%s %s', $exceptionMessage, $requestException);
            }

            throw new Exception($exceptionMessage);
        }
    }

    protected function handleNoChoices(string $endpoint): array
    {
        return [
            [
                'label' => __('Kan de "' . $endpoint . '" die horen bij de geselecteerde leverancier niet ophalen.', 'owc-gravityforms-zaaksysteem'),
            ],
        ];
    }
}
