<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Validation;

use OWC\Zaaksysteem\Foundation\ServiceProvider;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadHooks();
    }

    protected function loadHooks(): void
    {
        $this->validateTemplate();
    }

    /**
     * Validate if the wanted template requires an BSN from the current user session and is valid.
     */
    private function validateTemplate(): void
    {
        add_action('template_include', function ($template) {
            $templateName = str_replace(['.blade.php', '.php'], '', basename($template));

            /**
             * Filters the array of templates to validate.
             *
             * @since 1.1.1
             *
             * @param array $templatesToValidate Template names to validate
             */
            $templatesToValidate = apply_filters('owc_gravityforms_zaaksysteem_templates_to_validate', ['template-openzaak', 'template-mijn-taken']);

            if (! in_array($templateName, $templatesToValidate)) {
                return $template;
            }

            if (empty(ContainerResolver::make()->get('digid.current_user_bsn')) && 'template-openzaak' === $templateName) {
                return $this->returnForbidden();
            }

            return $template;
        }, 10, 1);
    }

    /**
     * Return a 403 forbidden page if the user has no access rights.
     *
     * @since 1.0.0
     */
    private function returnForbidden(): string
    {
        global $wp_query;

        $wp_query->set_403();

        status_header(403);

        add_filter('pre_get_document_title', function (?string $title) {
            return 'Geen toegang';
        }, 10, 1);

        return sprintf('%s/%s', OWC_GZ_ROOT_PATH, 'resources/views/403.php');
    }
}
