<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Routing\Controllers;

use Exception;
use OWC\Zaaksysteem\Endpoints\Filter\ZakenFilter;
use OWC\Zaaksysteem\Entities\Zaak;
use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

use OWC\Zaaksysteem\Support\Collection;

class SingleZaakRoutingController extends AbstractRoutingController
{
    public function register(): void
    {
        $this->addCustomRewriteRules();
        $this->allowCustomQueryVars();
        $this->includeObjectInTemplate();
    }

    /**
     * Implement custom routing for single 'zaak' pages
     *
     * The implementation for single 'zaken' requires:
     * - a page with 'zaak' as the slug
     * - the page to be connected with template 'template-single-zaak'
     * - the page to be requested with an identification and supplier in the URI
     */
    protected function addCustomRewriteRules(): void
    {
        add_action('init', function () {
            flush_rewrite_rules();

            add_rewrite_rule(
                'zaak/([a-zA-Z0-9-]+)/([a-zA-Z]+)/?$',
                'index.php?pagename=zaak&zaak_identification=$matches[1]&zaak_supplier=$matches[2]',
                'top'
            );
        });
    }

    /**
     * Add requested 'zaak' vars to the query variables.
     * This enables using the 'zaak' inside the template.
     */
    protected function allowCustomQueryVars(): void
    {
        add_action('query_vars', function (array $queryVars) {
            $queryVars[] = 'zaak_identification';
            $queryVars[] = 'zaak_supplier';

            return array_unique($queryVars);
        });
    }

    protected function includeObjectInTemplate(): void
    {
        add_action('template_include', function ($template) {
            if ($this->isTemplateSingleZaak($template)) {
                $this->handleSingleZaak();
            }

            return $template;
        }, 999, 1); // High priority so the validateTemplate method inside the ValidationServiceProvider runs first.
    }

    protected function handleSingleZaak(): void
    {
        set_query_var('zaak', $this->getZaak());
    }

    protected function getZaak(): ?Zaak
    {
        $identification = get_query_var('zaak_identification');
        $supplier = get_query_var('zaak_supplier');

        if (empty($identification) || empty($supplier)) {
            return null;
        }

        if (! $this->checkSupplier($supplier)) {
            return null;
        }

        $client = $this->container->getApiClient($supplier);

        $filter = new ZakenFilter();
        $filter->add('identificatie', $identification);
        $filter->byBsn(resolve('digid.current_user_bsn'));

        try {
            $zaak = $client->zaken()->filter($filter)->first() ?: null;
        } catch(Exception $e) {
            $zaak = null;
        }

        if (empty($zaak)) {
            return null;
        }

        // Enrich the 'zaak' with additional values.
        $zaak->setValue('steps', is_object($zaak->zaaktype) && $zaak->zaaktype->statustypen instanceof Collection ? $zaak->zaaktype->statustypen->sortByAttribute('volgnummer') : []);
        $zaak->setValue('status_history', $zaak->statussen);

        return $zaak;
    }
}
