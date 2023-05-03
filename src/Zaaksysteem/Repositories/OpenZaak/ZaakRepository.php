<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\OpenZaak;

use Exception;

use OWC\Zaaksysteem\Client\Client;
use OWC\Zaaksysteem\Entities\Rol;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakeigenschap;
use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Repositories\AbstractRepository;
use OWC\Zaaksysteem\Support\PagedCollection;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;

use function OWC\Zaaksysteem\Foundation\Helpers\decrypt;
use function Yard\DigiD\Foundation\Helpers\resolve;

class ZaakRepository extends AbstractRepository
{
    /**
     * Instance of the plugin.
     */
    protected Plugin $plugin;

    /**
     * Construct the repository.
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Get the api client.
     */
    protected function getApiClient(): Client
    {
        return $this->plugin->getContainer()->get('oz.client');
    }

    /**
     * Get all available "roltypen".
     */
    public function getRolTypen(): PagedCollection
    {
        $client = $this->getApiClient();

        return $client->roltypen()->all();
    }

    /**
     * Create "zaak".
     */
    public function addZaak($entry, $form): ?Zaak
    {
        $client = $this->getApiClient();

        $identifier = $form[sprintf('%s-form-setting-%s-identifier', OWC_GZ_PLUGIN_SLUG, 'openzaak')];

        $rsin = $this->plugin->getContainer()->get('rsin');

        if (empty($rsin)) {
            throw new Exception('RSIN should not be empty in the Gravity Forms Settings');
        }

        $args = [
            'bronorganisatie' => $rsin ?? '',
            'verantwoordelijkeOrganisatie' => $rsin ?? '',
            'zaaktype' => $identifier ?? '',
            'registratiedatum' => date('Y-m-d'),
            'startdatum' => date('Y-m-d'),
            'omschrijving' => '',
            'informatieobject' => ''
        ];

        $zaak = $client->zaken()->create(new Zaak($args, $client::CLIENT_NAME));

        // @todo why does it say this role already exists?
        // $this->addRolToZaak($zaak->url);

        $this->addZaakEigenschappen($zaak, $form['fields'], $entry);

        return $zaak;
    }

    /**
     * Add "zaak" properties.
     */
    public function addZaakEigenschappen(Zaak $zaak, $fields, $entry): void
    {
        $mapping = $this->fieldMapping($fields, $entry);

        foreach ($mapping as $value) {
            $property = [
                'zaak' => $zaak->uri,
                'eigenschap' => $value['eigenschap'],
                'waarde' => $value['waarde'],
            ];

            $client = $this->getApiClient();

            try {
                $client->zaakeigenschappen()->create(
                    $zaak->uuid,
                    new Zaakeigenschap($property, $client::CLIENT_NAME)
                );
            } catch (BadRequestError $e) {
                $e->getInvalidParameters();
            }
        }
    }

    /**
     * Assign a submitter to the "zaak".
     */
    public function addRolToZaak(string $zaakUrl): ?Rol
    {
        $client = $this->getApiClient();
        $rolTypen = $this->getRolTypen();
        $rol = null;

        foreach ($rolTypen as $rolType) {
            if ($rolType['omschrijvingGeneriek'] !== 'initiator') {
                continue;
            }

            $args = [
                'zaak' => $zaakUrl,
                'betrokkeneType' => 'natuurlijk_persoon',
                'roltype' => $rolType['url'],
                'roltoelichting' => 'De indiener van de zaak.',
                'betrokkeneIdentificatie' => [
                    'inpBsn' => $this->resolveCurrentBsn()
                ]
            ];

            try {
                $rol = $client->rollen()->create(new Rol($args, $client::CLIENT_NAME));
            } catch (BadRequestError $e) {
                $e->getInvalidParameters();
            }
        }

        return $rol;
    }

    /**
     * @todo move this to separate handler
     */
    protected function resolveCurrentBsn(): string
    {
        $bsn = resolve('session')->getSegment('digid')->get('bsn');

        return decrypt($bsn);
    }
}
