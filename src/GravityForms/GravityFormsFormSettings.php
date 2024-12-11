<?php

namespace OWC\Zaaksysteem\GravityForms;

use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\DecosClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\OpenZaakClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\ProcuraClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\RxMissionClient;
use OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Clients\XxllncClient;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

class GravityFormsFormSettings
{
    protected string $prefix = OWC_GZ_PLUGIN_SLUG;

    /**
     * Add form based settings.
     */
    public function addFormSettings(array $fields, array $form): array
    {
        $supplierChoices = [
            [
                'name' => "{$this->prefix}-form-setting-supplier-none",
                'label' => __('Selecteer leverancier', 'owc-gravityforms-zaaksysteem'),
                'value' => 'none',
            ],
        ];
        if (ContainerResolver::make()->get('oz.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-openzaak",
                'label' => __('OpenZaak', 'owc-gravityforms-zaaksysteem'),
                'value' => 'openzaak',
            ];
        }
        if (ContainerResolver::make()->get('dj.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-decos-join",
                'label' => __('Decos Join', 'owc-gravityforms-zaaksysteem'),
                'value' => 'decos-join',
            ];
        }
        if (ContainerResolver::make()->get('rx.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-rx-mission",
                'label' => __('Rx.Mission', 'owc-gravityforms-zaaksysteem'),
                'value' => 'rx-mission',
            ];
        }
        if (ContainerResolver::make()->get('xxllnc.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-xxllnc",
                'label' => __('Xxllnc', 'owc-gravityforms-zaaksysteem'),
                'value' => 'xxllnc',
            ];
        }

        if (ContainerResolver::make()->get('procura.enabled')) {
            $supplierChoices[] = [
                'name' => "{$this->prefix}-form-setting-supplier-procura",
                'label' => __('Procura', 'owc-gravityforms-zaaksysteem'),
                'value' => 'procura',
            ];
        }

        $fields['owc-gravityforms-zaaksysteem'] = [
            'title' => esc_html__('Zaaksysteem', 'owc-gravityforms-zaaksysteem'),
            'description' => esc_html__('Om de snelheid te verhogen worden de instellingen van leveranciers pas opgehaald na het kiezen van een leverancier. Dit betekent dat de pagina herladen moet worden na het selecteren van een leverancier.', 'owc-gravityforms-zaaksysteem'),
            'fields' => [
                [
                    'name' => "{$this->prefix}-form-setting-supplier",
                    'default_value' => "{$this->prefix}-form-setting-supplier-none",
                    'tooltip' => '<h6>' . __('Selecteer een leverancier', 'owc-gravityforms-zaaksysteem') . '</h6>' . __('Kies een Zaaksysteem leverancier. Let op dat je ook de instellingen van de leverancier moet configureren in de hoofdinstellingen van Gravity Forms.', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'select',
                    'label' => esc_html__('Selecteer een leverancier', 'owc-gravityforms-zaaksysteem'),
                    'choices' => $supplierChoices,
                ],
                [
                    'name' => "{$this->prefix}-form-setting-supplier-manually",
                    'default_value' => "1",
                    'tooltip' => '<h6>' . __('Leverancier instellingen', 'owc-gravityforms-zaaksysteem') . '</h6>' . __('Kies hoe de leverancier instellingen geconfigureerd moeten worden.', 'owc-gravityforms-zaaksysteem'),
                    'type' => 'radio',
                    'label' => esc_html__('Leverancier instellingen', 'owc-gravityforms-zaaksysteem'),
                    'choices' => [
                        [
                            'name' => "{$this->prefix}-form-setting-supplier-manually-disabled",
                            'label' => __('Selecteer instellingen (opgehaald vanuit zaaksysteem)', 'owc-gravityforms-zaaksysteem'),
                            'value' => '0',
                        ],
                        [
                            'name' => "{$this->prefix}-form-setting-supplier-manually-enabled",
                            'label' => __('Configureer instellingen handmatig (invoeren van URL\'s)', 'owc-gravityforms-zaaksysteem'),
                            'value' => '1',
                        ],
                    ],
                ],
            ],
        ];

        $fields['owc-gravityforms-zaaksysteem']['fields'] = $this->getFieldsBySupplier($form, $fields['owc-gravityforms-zaaksysteem']['fields']);

        return $fields;
    }

    /**
     * Retrieves the fields associated with a specific supplier based on the form settings and merge with existing fields.
     */
    protected function getFieldsBySupplier(array $form, array $fields): array
    {
        $supplierSetting = $form["{$this->prefix}-form-setting-supplier"] ?? '';
        $manual = $form["{$this->prefix}-form-setting-supplier-manually"] ?? '0';
        $suppliersFields = $this->fieldsBySupplier();

        if (empty($supplierSetting) || empty($suppliersFields[$supplierSetting][$manual ? 'manual_setting' : 'select_setting'])) {
            return $fields;
        }

        return array_merge($fields, $suppliersFields[$supplierSetting][$manual ? 'manual_setting' : 'select_setting']);
    }

    /**
     * Fields associated with suppliers, used for matching the fields of the selected supplier in form settings.
     * This approach minimizes unnecessary requests to multiple sources that are not needed. Because only one supplier can be selected.
     */
    protected function fieldsBySupplier(): array
    {
        $fields = [];
        if (ContainerResolver::make()->get('oz.enabled')) {
            $fields['openzaak'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-openzaak-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openzaak'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new OpenZaakClient(ContainerResolver::make()->getApiClient('openzaak')))->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-openzaak-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openzaak'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new OpenZaakClient(ContainerResolver::make()->getApiClient('openzaak')))->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-openzaak-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openzaak'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-openzaak-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['openzaak'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('rx.enabled')) {
            $fields['rx-mission'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-rx-mission-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['rx-mission'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new RxMissionClient(ContainerResolver::make()->getApiClient('rx-mission')))->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-rx-mission-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['rx-mission'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new RxMissionClient(ContainerResolver::make()->getApiClient('rx-mission')))->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-rx-mission-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['rx-mission'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-rx-mission-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['rx-mission'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('xxllnc.enabled')) {
            $fields['xxllnc'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-xxllnc-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['xxllnc'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new XxllncClient(ContainerResolver::make()->getApiClient('xxllnc')))->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-xxllnc-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['xxllnc'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new XxllncClient(ContainerResolver::make()->getApiClient('xxllnc')))->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-xxllnc-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['xxllnc'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-xxllnc-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['xxllnc'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('procura.enabled')) {
            $fields['procura'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-procura-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['procura'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new ProcuraClient(ContainerResolver::make()->getApiClient('procura')))->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-procura-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['procura'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new ProcuraClient(ContainerResolver::make()->getApiClient('procura')))->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-procura-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['procura'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-procura-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['procura'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (ContainerResolver::make()->get('dj.enabled')) {
            $fields['decos-join'] = [
                'select_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-decos-join-identifier",
                        'type' => 'select',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['decos-join'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new DecosClient(ContainerResolver::make()->getApiClient('decos')))->zaaktypen(),
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-decos-join-information-object-type",
                        'type' => 'select',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['decos-join'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['0'],
                                ],
                            ],
                        ],
                        'choices' => (new DecosClient(ContainerResolver::make()->getApiClient('decos')))->informatieobjecttypen(),
                    ],
                ],
                'manual_setting' => [
                    [
                        'name' => "{$this->prefix}-form-setting-decos-join-identifier-manual",
                        'type' => 'text',
                        'label' => esc_html__('Zaaktype', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['decos-join'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => "{$this->prefix}-form-setting-decos-join-information-object-type-manual",
                        'type' => 'text',
                        'label' => esc_html__('Informatie object type', 'owc-gravityforms-zaaksysteem'),
                        'dependency' => [
                            'live' => true,
                            'fields' => [
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier",
                                    'values' => ['decos-join'],
                                ],
                                [
                                    'field' => "{$this->prefix}-form-setting-supplier-manually",
                                    'values' => ['1'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

        }

        return $fields;
    }
}
