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
     * Get all available roles.
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
        $identifier = $form['owc-gravityforms-zaaksysteem-form-setting-openzaak-identifier'];
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
        //$this->addZaakEigenschappen($args, $form['fields'], $entry);
        $this->addZaakEigenschappen($zaak, $args, $form['fields'], $entry);

        return $zaak;
    }

    /**
     * Add "zaak" properties.
     *
     * @todo: call rollen or zaken api
     */
    public function addZaakEigenschappen(Zaak $zaak, $args, $fields, $entry): ?Zaakeigenschap
    {
        //$mapping = $this->fieldMapping($args, $fields, $entry);

        $mapping = [
            'zaak' => $zaak->uri,
            'eigenschap' => 'https://open-zaak.test.buren.opengem.nl/zaak/api/v1/zaak/cd484e04-9a88-424f-9b5d-e31cabb23623/zaakeigenschappen/12345',
            'waarde' => 'Hello World',
        ];

        $client = $this->getApiClient();
        $client->zaakeigenschappen()->create($zaak->uuid, new Zaakeigenschap($mapping, $client::CLIENT_NAME));

        return null;
    }

    public function getZaakEigenschappen($zaak): ?Zaakeigenschap
    {
        $client = $this->getApiClient();

        return $client->zaakeigenschappen()->all($zaak);
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
