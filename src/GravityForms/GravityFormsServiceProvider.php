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

        // Mijn taken
        add_action('gform_pre_render', function ($form) {
            $taak = get_query_var('taak');

            if (! is_object($taak)) {
                return $form;
            }

            $zaak = $taak->getValue('zaak');

            if ($taak->informationObjectURL()) { // $fields[0] moet wel upload veld zijn
                $form['fields'][0]->linkedFieldValueZGW = 'informatieobject';
                $form['fields'][0]->linkedFieldValueDocumentType = $taak->informationObjectURL();
            }

            if ($taak->supplier()) {
                // Zoeken naar veld met label 'supplier'
                $form['fields'][1]->defaultValue = $taak->supplier();
            }

            if (! is_null($zaak)) {
                // Zoeken naar veld met label 'zaak'
                $form['fields'][2]->defaultValue = $zaak->id;
            }

            $form['fields'][3]->defaultValue = $taak->id;
            $form['fields'][4]->defaultValue = $taak->title();
            $form['fields'][5]->defaultValue = $taak->informationObjectURL();

            return $form;
        }, 10, 1);
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
