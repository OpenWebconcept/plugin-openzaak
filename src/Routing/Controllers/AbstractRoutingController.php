<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Routing\Controllers;

use OWC\Zaaksysteem\Resolvers\ContainerResolver;

abstract class AbstractRoutingController
{
    protected ContainerResolver $container;

    public function __construct()
    {
        $this->container = ContainerResolver::make();
    }

    abstract public function register(): void;
    abstract protected function addCustomRewriteRules(): void;
    abstract protected function allowCustomQueryVars(): void;
    abstract protected function includeObjectInTemplate(): void;

    /**
     * The supplier is retrieved from the requested URL therefore making it vulnerable for unwanted changes.
     */
    protected function checkSupplier(string $supplier): bool
    {
        $suppliers = $this->container->get('config')->get('suppliers');

        return in_array($supplier, array_keys($suppliers));
    }

    /**
     * Some handle functions require the 'template-single-zaak' template.
     */
    protected function isTemplateSingleZaak(string $template): bool
    {
        $templateName = str_replace(['.blade.php', '.php'], '', basename($template));

        return 'template-single-zaak' === $templateName;
    }

    /**
     * Some handle functions require the 'template-single-taak' template.
     */
    protected function isTemplateSingleTaak(string $template): bool
    {
        $templateName = str_replace(['.blade.php', '.php'], '', basename($template));

        return 'template-single-taak' === $templateName;
    }
}
