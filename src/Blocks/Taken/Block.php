<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks\Taken;

use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\TakenFilter;
use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\Collection;

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
            // return 'Er is geen geldig BSN gevonden waardoor er geen taken opgehaald kunnen worden.';
        }

        $this->client = ContainerResolver::make()->getApiClient($attributes['zaakClient'] ?? 'openzaak');

        if (! $this->client->supports('zaken')) {
            return __('Het Mijn Taken overzicht is niet beschikbaar.', 'owc-gravityforms-zaaksysteem');
        }

        $taken = get_transient($this->uniqueTransientKey($attributes));

        if ($taken instanceof Collection && $taken->isNotEmpty()) {
            return $this->returnView($attributes, $taken);
        }

        try {
            $taken = $this->handleZakenTaken($attributes);
        } catch (Exception $e) {
            $taken = collect();
        }

        if ($taken->isEmpty()) {
            return __('Er zijn op dit moment geen taken beschikbaar.', 'owc-gravityforms-zaaksysteem');
        }

        set_transient($this->uniqueTransientKey($attributes), $taken, 500);

        return $this->returnView($attributes, $taken);
    }

    protected function getCurrentUserBsn(): string
    {
        $bsn = resolve('digid.current_user_bsn');

        /**
         * TEMP: signicat plugin has some changes pending which requires another implementation.
         */
        if (empty($bsn)) {
            $isLoggedIn = apply_filters('owc_digid_is_logged_in', false, 'digid');

            if ($isLoggedIn) {
                $userInfo = apply_filters('owc_digid_userdata', null, 'digid');
                $bsn = $userInfo->getBsn();
            }

            return ! empty($bsn) && is_string($bsn) ? $bsn : '';
        }

        return $bsn;
    }

    protected function handleZakenTaken(array $attributes): Collection
    {
        $zaken = $this->getZaken($attributes);

        return $this->getTaken($zaken);
    }

    /**
     * Based on the configured attributes and the bsn of the current user.
     */
    protected function uniqueTransientKey(array $attributes): string
    {
        $attributes['bsnCurrentUser'] = $this->getCurrentUserBsn();
        $attributes['type'] = 'taken';

        return md5(json_encode($attributes));
    }

    protected function getZaken(array $attributes): Collection
    {
        // return Collection::collect([
        //     'https://api.accept.common-gateway.commonground.nu/api/zrc/v1/zaken/f5d1c9b7-e3a9-485d-98cb-1667e1d0537c',
        // ]);

        // return $this->client->zaken()->all();
        // When mijn-taken api is properly configured this should be activated again.
        $filter = new ZakenFilter();
        $filter = $this->handleFilterBSN($filter, $attributes);

        return $this->client->zaken()->filter($filter);
    }

    protected function handleFilterBSN(ZakenFilter $filter, array $attributes): ZakenFilter
    {
        if (! ($attributes['byBSN'] ?? false)) {
            return $filter;
        }

        $filter->byBsn($this->getCurrentUserBsn());

        return $filter;
    }

    protected function getTaken(Collection $zaken): Collection
    {
        $taken = [];

        foreach ($zaken as $zaak) {
            $filter = new TakenFilter();
            $filter->byZaak($zaak);
            $fetchedTaken = $this->client->taken()->filter($filter);

            if ($fetchedTaken->isNotEmpty()) {
                $taken[] = $fetchedTaken->toArray();
            }
        }
        // Misschien nog filteren hier?

        return Collection::collect($taken)->flattenAndAssign(function ($carry, $item) {
            if (is_array($item)) {
                return array_merge($carry, $item);
            }

            $carry[] = $item;

            return $carry;
        }, []);
    }

    protected function returnView(array $attributes, Collection $taken): string
    {
        if ('default' === $attributes['view']) {
            return view('blocks/mijn-taken/overview-taken.php', ['taken' => $taken]);
        }

        if ('current' === $attributes['view']) {
            /**
             * Before reviewing this clause, it's important to note the limitations of the Taken endpoint.
             * The Taken endpoint lacks native support for limiting results by a specific number.
             * Consequently, the 'take' method is used. However, it's worth noting that this approach may not be efficient,
             * especially when a resident has a substantial number of 'zaken'.
             *
             * Ideally, we would be able to apply additional filtering based on the status of Taken, such as 'current' and 'closed'.
             */
            $limit = $attributes['numberOfItems'] ?? 2;

            return view('blocks/mijn-taken/overview-taken-current.php', ['taken' => $taken->take((int) $limit)]);
        }

        return view('blocks/mijn-taken/overview-taken.php', ['taken' => $taken]);
    }
}
