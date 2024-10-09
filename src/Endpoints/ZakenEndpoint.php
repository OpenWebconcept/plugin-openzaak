<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Entities\Status;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Http\Response;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Support\PagedCollection;

class ZakenEndpoint extends Endpoint
{
    protected string $endpoint = 'zaken';
    protected string $entityClass = Zaak::class;

    public function all(): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUriWithExpand($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Zaak
    {
        $response = $this->httpClient->get(
            $this->buildUriWithExpand(sprintf('%s/%s', $this->endpoint, $identifier)),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\ZakenFilter $filter): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUriWithExpand($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    protected function buildEntity($data): Entity
    {
        $class = $this->entityClass;
        $zaak = new $class($data, $this->client::CALLABLE_NAME, $this->client::CLIENT_NAME);

        $statusToelichting = $zaak->status instanceof Status ? $zaak->status->statustype->statusExplanation() : '';
        $zaak->setValue('leverancier', $zaak->getClientNamePretty());
        $zaak->setValue('steps', $this->handleProcessStatusses($this->getStatussenSorted($zaak), $statusToelichting));
        $zaak->setValue('status_history', $zaak->statussen);
        $zaak->setValue('information_objects', $zaak->zaakinformatieobjecten);
        $zaak->setValue('status_explanation', $statusToelichting);
        $zaak->setValue('result', $zaak->resultaat);
        $zaak->setValue('image', ContainerResolver::make()->get('zaak_image'));
        $zaak->setValue('zaaktype_description', $zaak->zaaktype->omschrijvingGeneriek ?? '');

        return $zaak;
    }

    protected function getStatussenSorted(Entity $zaak): Collection
    {
        $zaakType = $zaak->zaaktype;
        $statusTypen = is_object($zaakType) ? $zaakType->statustypen : null;

        if (! $statusTypen instanceof Collection) {
            return Collection::collect([]);
        }

        return $statusTypen->sortByAttribute('volgnummer')->mapWithKeys(function ($key, $statusType) {
            /**
             * Ensures uniform usage of 'volgnummers' across different clients.
             * Set the 'volgnummer' attribute of the statustype to its position in the collection (1-based index).
             */
            $statusType->setValue('volgnummer', $key + 1);

            return $statusType;
        });
    }

    protected function handleProcessStatusses(Collection $statussen, string $statusToelichting): Collection
    {
        if ($statussen->isEmpty()) {
            return $statussen;
        }

        // Not possible to match with a status connected to a 'Zaak', set the first status as current.
        if (empty($statusToelichting)) {
            $currentVolgnummer = $statussen->first()->volgnummer();

            return $this->addProcessStatusses($statussen, $currentVolgnummer);
        }

        // Get the current status which matches with the status connected to a 'Zaak'.
        $filtered = $statussen->filter(function ($status) use ($statusToelichting) {
            return strtolower($status->statusExplanation()) === strtolower($statusToelichting);
        });

        $currentVolgnummer = $filtered->first() ? $filtered->first()->volgnummer() : null;

        if (empty($currentVolgnummer)) {
            return $statussen;
        }

        return $this->addProcessStatusses($statussen, $currentVolgnummer);
    }

    protected function addProcessStatusses(Collection $statussen, string $currentVolgnummer)
    {
        return $statussen->map(function ($status) use ($currentVolgnummer) {
            $volgnummer = (int) $status->volgnummer();
            $currentNum = (int) $currentVolgnummer;

            if ($volgnummer < $currentNum) {
                $status->setValue('processStatus', 'past');
            } elseif ($volgnummer === $currentNum) {
                $status->setValue('processStatus', 'current');
            } else {
                $status->setValue('processStatus', 'future');
            }

            return $status;
        });
    }

    public function create(Zaak $model): Zaak
    {
        /**
         * @todo add field validation
         * These fields are required on a Zaak model:
         * - bronorganisatie
         * - zaaktype (URI)
         * - verantwoordelijkeOrganisatie
         * - startdatum
         * Additionally, these rules are required to pass:
         * - zaaktype != concept
         * - laatsteBetaaldatum > NOW
         */
        $response = $this->httpClient->post(
            $this->buildUri($this->endpoint),
            $model->toJson(),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function delete(string $zaakURL): Response
    {
        $response = $this->httpClient->delete(
            $zaakURL,
            $this->buildRequestOptions()
        );

        return $this->handleResponse($response);
    }
}
