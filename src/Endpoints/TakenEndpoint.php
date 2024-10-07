<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Taak;
use OWC\Zaaksysteem\Support\PagedCollection;

class TakenEndpoint extends Endpoint
{
    protected string $apiType = 'taken';
    protected string $endpoint = 'taken';
    protected string $entityClass = Taak::class;

    public function filter(Filter\TakenFilter $filter): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }
}
