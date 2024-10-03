<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Zaken;

use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;
use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use OWC\Zaaksysteem\Entities\Zaaktype;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\Collection;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class Block
{
    protected Client $client;

    public function render($attributes, $rendered, $editor)
    {
        // Bail early when in editor.
        if (is_admin() || defined('REST_REQUEST')) {
            return;
        }

        if (! $this->getCurrentUserBsn()) {
            return 'Er is geen geldig BSN gevonden waardoor er geen zaken opgehaald kunnen worden.';
        }

        $this->client = ContainerResolver::make()->getApiClient($attributes['zaakClient'] ?? 'openzaak');

        if (! $this->client->supports('zaken')) {
            return __('Het Mijn Zaken overzicht is niet beschikbaar.', 'owc-gravityforms-zaaksysteem');
        }

        $zaken = get_transient($this->uniqueTransientKey($attributes));

        if ($zaken instanceof Collection && $zaken->isNotEmpty()) {
            return $this->returnView($attributes, $zaken);
        }

        try {
            $zaken = $this->handleZaken($attributes);
        } catch (Exception $e) {
            $zaken = collect();
        }

        if ($zaken->isEmpty()) {
            return __('Er zijn op dit moment geen zaken beschikbaar.', 'owc-gravityforms-zaaksysteem');
        }

        set_transient($this->uniqueTransientKey($attributes), $zaken, 500);

        return $this->returnView($attributes, $zaken);
    }

    protected function getCurrentUserBsn(): string
    {
        $bsn = resolve('digid.current_user_bsn');

        /**
         * TEMP: signicat plugin has some changes pending which requires another implementation.
         */
        if (empty($bsn)) {
            $isLoggedIn = apply_filters('owc_siginicat_openid_is_user_logged_in', false, 'digid');

            if ($isLoggedIn) {
                $userInfo = apply_filters('owc_signicat_openid_user_info', [], 'digid');
                $bsn = $userInfo['sub'] ?? '';
            }

            return ! empty($bsn) && is_string($bsn) ? $bsn : '';
        }

        return $bsn;
    }

    protected function handleZaken(array $attributes): Collection
    {
        if (! $attributes['combinedClients']) {
            return $this->getZaken($attributes);
        }

        return $this->getCombinedZaken($attributes);
    }

    /**
     * Based on the configured attributes and the bsn of the current user.
     */
    protected function uniqueTransientKey(array $attributes): string
    {
        $attributes['bsnCurrentUser'] = $this->getCurrentUserBsn();

        return md5(json_encode($attributes));
    }

    protected function getZaken(array $attributes): Collection
    {
        $filter = new ZakenFilter();
        $filter = $this->handleFilterOrdering($filter, $attributes);
        $filter = $this->handleFilterBSN($filter, $attributes);
        $filter = $this->handleFilterZaaktype($filter, $attributes);

        return $this->client->zaken()->filter($filter);
    }

    protected function getCombinedZaken(array $attributes): Collection
    {
        $zaken = [];
        $suppliers = ContainerResolver::make()->get('config')->get('suppliers');

        foreach (array_keys($suppliers) as $supplier) {
            $client = ContainerResolver::make()->getApiClient($supplier);

            $filter = new ZakenFilter();
            $filter = $this->handleFilterOrdering($filter, $attributes);
            $filter = $this->handleFilterBSN($filter, $attributes);
            $filter = $this->handleFilterZaaktype($filter, $attributes, $client);

            try {
                $zaken[] = $client->zaken()->filter($filter)->all();
            } catch (Exception $e) {
                continue;
            }
        }

        return Collection::collect($zaken)->flattenAndAssign(function ($carry, $item) {
            if (is_array($item)) {
                return array_merge($carry, $item);
            }

            $carry[] = $item;

            return $carry;
        }, []);
    }

    protected function handleFilterOrdering(ZakenFilter $filter, array $attributes): ZakenFilter
    {
        if (empty($attributes['orderBy'])) {
            return $filter;
        }

        $filter->orderBy($attributes['orderBy']);

        return $filter;
    }

    protected function handleFilterBSN(ZakenFilter $filter, array $attributes): ZakenFilter
    {
        if (! $attributes['byBSN']) {
            return $filter;
        }

        $filter->byBsn($this->getCurrentUserBsn());

        return $filter;
    }

    protected function handleFilterZaaktype(ZakenFilter $filter, array $attributes, ?Client $client = null): ZakenFilter
    {
        if (! is_string($attributes['zaaktypeFilter'])) {
            return $filter;
        }

        $identifications = json_decode($attributes['zaaktypeFilter'], true);

        if (! is_array($identifications) || empty($identifications)) {
            return $filter;
        }

        foreach ($this->zaaktypeIdentificationsToURL($identifications, $client) as $zaaktype) {
            $filter->add('zaaktype', $zaaktype);
            // $filter->add('identificatie', $zaaktype); // Is not supported yet.
        }

        return $filter;
    }

    /**
     * For testing purposes, use 'JB007' as identification.
     * This method should be removed when filtering on 'Zaaktype' identification is supported.
     * Don't forget to unuse this method in the foreach above as well.
     */
    protected function zaaktypeIdentificationsToURL(array $identifications, ?Client $client = null): array
    {
        $page = 1;
        $zaaktypen = [];

        $client = $client ? $client : $this->client; // Use $client when 'zaken' from all the suppliers are retrieved combined.

        while ($page) {
            $result = $client->zaaktypen()->all((new ResultaattypenFilter())->page($page));
            $zaaktypen = array_merge($zaaktypen, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        return (array) Collection::collect($zaaktypen)->map(function (Zaaktype $zaaktype) use ($identifications) {
            if (! in_array($zaaktype->identificatie, $identifications)) {
                return '';
            }

            return $zaaktype->url;
        })->filter(function ($url) {
            return ! empty($url);
        })->all();
    }

    protected function returnView(array $attributes, Collection $zaken)
    {
        if ('tabs' === $attributes['view']) {
            return view('blocks/mijn-zaken/overview-zaken-tabs.php', ['zaken' => $zaken]);
        }

        if ('current' === $attributes['view']) {
            /**
             * Before reviewing this clause, it's important to note the limitations of the Zaken endpoint.
             * The Zaken endpoint lacks native support for limiting results by a specific number.
             * Consequently, the 'take' method is used. However, it's worth noting that this approach may not be efficient,
             * especially when a resident has a substantial number of 'zaken'.
             *
             * Ideally, we would be able to apply additional filtering based on the status of Zaken, such as 'current' and 'closed'.
             */
            $limit = $attributes['numberOfItems'] ?? 2;

            return view('blocks/mijn-zaken/overview-zaken-current.php', ['zaken' => $zaken->take((int) $limit)]);
        }

        return view('blocks/mijn-zaken/overview-zaken.php', ['zaken' => $zaken]);
    }
}
