<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Routing\Controllers;

use Exception;
use GFAPI;
use OWC\Zaaksysteem\Entities\Taak;
use WP_Rewrite;

class SingleTaakRoutingController extends AbstractRoutingController
{
    public function register(): void
    {
        $this->addCustomRewriteRules();
        $this->allowCustomQueryVars();
        $this->includeObjectInTemplate();
    }

    /**
     * Implement custom routing for single 'taak' pages
     *
     * The implementation for single 'taken' requires:
     * - a page with 'taak' as the slug
     * - the page to be connected with template 'template-single-taak'
     * - the page to be requested with an identification and supplier in the URI
     */
    protected function addCustomRewriteRules(): void
    {
        add_action('generate_rewrite_rules', function (WP_Rewrite $rewrite) {
            $rules = [
                'taak/([a-zA-Z0-9.-]+)/([a-zA-Z-]+)/?$' => 'index.php?pagename=taak&taak_identification=$matches[1]&zaak_supplier=$matches[2]',
            ];

            $rewrite->rules = $rules + $rewrite->rules;
        });
    }

    /**
     * Add requested 'zaak' vars to the query variables.
     * This enables using the 'zaak' inside the template.
     */
    protected function allowCustomQueryVars(): void
    {
        add_action('query_vars', function (array $queryVars) {
            $queryVars[] = 'taak_identification';
            $queryVars[] = 'zaak_supplier';

            return array_unique($queryVars);
        });
    }

    protected function includeObjectInTemplate(): void
    {
        add_action('template_include', function ($template) {
            if ($this->isTemplateSingleTaak($template)) {
                $this->handleSingleTaak();
            }

            return $template;
        }, 999, 1); // High priority so the validateTemplate method inside the ValidationServiceProvider runs first.
    }

    protected function handleSingleTaak(): void
    {
        $taak = $this->getTaak();
        set_query_var('taak', $taak);
        set_query_var('formID', $this->getForm($taak));
    }

    protected function getTaak(): ?Taak
    {
        $identification = get_query_var('taak_identification');
        $supplier = get_query_var('zaak_supplier');

        if (empty($identification) || empty($supplier)) {
            return null;
        }

        if (! $this->checkSupplier($supplier)) {
            return null;
        }

        $client = $this->container->getApiClient($supplier);

        try {
            $taak = $client->taken()->get($identification);
        } catch (Exception $e) {
            $taak = null;
        }

        return $taak instanceof Taak ? $taak : null;
    }

    protected function getForm(?Taak $taak)
    {
        if (is_null($taak)) {
            return 0;
        }

        $forms = GFAPI::get_forms();

        if (! is_array($forms) || ! count($forms)) {
            return 0;
        }

        if ($taak->informationObjectURL()) {
            $cssClass = 'taak-informatieobject';
        } // Uitbreiden nog met andere opties zoals zaakeigenschap.

        if (! isset($cssClass)) {
            return 0;
        }

        $form = array_filter($forms, function ($form) use ($cssClass) {
            return isset($form['cssClass']) && $cssClass === $form['cssClass'];
        });

        $form = reset($form);

        return isset($form['id']) ? $form['id'] : 0;
    }
}
