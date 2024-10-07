<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use GFAddOn;
use GFForms;
use OWC\Zaaksysteem\Foundation\ServiceProvider;
use OWC\Zaaksysteem\GravityForms\Controllers\GravityFormsPaymentController;

class GravityFormsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadHooks();
        $this->paymentHooks();
        $this->registerSettingsAddon();
    }

    protected function loadHooks(): void
    {
        if (! class_exists('GFForms')) {
            return;
        }

        $gravityFormsFieldSettings = new GravityFormsFieldSettings($this->plugin);

        $this->plugin->loader->addFilter('gform_after_submission', new GravityForms(), 'GFFormSubmission', 10, 2);
        $this->plugin->loader->addAction('gform_field_standard_settings', $gravityFormsFieldSettings, 'addSelect', 10, 2);
        $this->plugin->loader->addAction('gform_editor_js', $gravityFormsFieldSettings, 'addSelectScript', 10, 0);
        $this->plugin->loader->addFilter('gform_form_settings_fields', new GravityFormsFormSettings(), 'addFormSettings', 10, 2);

        // dd(\OWC\Zaaksysteem\Resolvers\ContainerResolver::make()->get('xxllnc.mijn_taken_uri'));
    }

    /**
     * Registers hooks that are triggered when a form requires a payment.
     *
     * These hooks perform necessary actions based on the outcome of the payment process:
     * - If the payment fails, the created Zaak will be deleted.
     * - If the payment is completed successfully, a note will be added to the entry.
     */
    private function paymentHooks(): void
    {
        $paymentController = new GravityFormsPaymentController();
        add_action('gform_post_payment_action', [$paymentController, 'postPaymentFailed'], 10, 2);
        add_action('gform_post_payment_completed', [$paymentController, 'postPaymentCompleted'], 10, 2);
    }

    private function registerSettingsAddon(): void
    {
        if (! method_exists('GFForms', 'include_addon_framework')) {
            return;
        }

        GFForms::include_addon_framework();
        GFAddOn::register(GravityFormsAddon::class);
        GravityFormsAddon::get_instance();
    }
}
