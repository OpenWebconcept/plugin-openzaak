<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Entities\Taak;
use OWC\Zaaksysteem\Support\PagedCollection;

class TakenEndpoint extends Endpoint
{
    protected string $endpoint = 'taken';
    protected string $entityClass = Taak::class;

    public function get(string $identifier): ?Taak
    {
        $response = $this->httpClient->get(
            $this->buildUri(sprintf('%s/%s', $this->endpoint, $identifier)),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\TakenFilter $filter): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    public function update(string $taakURL, array $args): Taak
    {
        $response = $this->httpClient->update(
            $this->buildUri(sprintf('%s/%s', $this->endpoint, $taakURL)),
            $args,
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    protected function mapEntities(array $data): array
    {
        $entities = array_map(function ($item) {
            return is_array($item) && isset($item['extraData']['leverancier']) ? $this->buildEntity($item) : null;
        }, $data);

        return array_filter($entities);
    }

    protected function buildEntity($data): Entity
    {
        $clientabbreviation = \OWC\Zaaksysteem\Resolvers\ContainerResolver::make()->get(sprintf('%s.abbr', strtolower($data['extraData']['leverancier'])));
        $client = \OWC\Zaaksysteem\Resolvers\ContainerResolver::make()->get(sprintf('%s.client', $clientabbreviation));
        $class = $this->entityClass;
        $taak = new $class($data, $client::CALLABLE_NAME, $client::CLIENT_NAME);

        return $taak;
    }
}
