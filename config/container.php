<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem;

use DI\Container;
use OWC\Zaaksysteem\Resolvers\DigiDBsnResolver;

/**
 * Link interfaces to their concretions.
 */

return [
    /**
     * OpenZaak configuration.
     */
    'openzaak.abbr' => 'oz',
    'oz.enabled' => function (Container $container) {
        return (bool) $container->make('gf.setting', ['-suppliers-openzaak-enabled']);
    },
    'oz.client' => fn (Container $container) => $container->get(Clients\OpenZaak\Client::class),
    'oz.catalogi_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-catalogi-url']);
    },
    'oz.documenten_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-documenten-url']);
    },
    'oz.zaken_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-zaken-url']);
    },
    'oz.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-client-id']);
    },
    'oz.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-client-secret']);
    },
    'oz.authenticator' => function (Container $container) {
        return $container->get(Clients\OpenZaak\Authenticator::class);
    },

    /**
     * Decos JOIN configuration.
     */
    'decosjoin.abbr' => 'dj',
    'dj.enabled' => function (Container $container) {
        return (bool) $container->make('gf.setting', ['-suppliers-decos-join-enabled']);
    },
    'dj.client' => fn (Container $container) => $container->get(Clients\DecosJoin\Client::class),
    'dj.catalogi_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-catalogi-url']);
    },
    'dj.documenten_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-documenten-url']);
    },
    'dj.zaken_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-zaken-url']);
    },
    'dj.token_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-token-url']);
    },
    'dj.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-client-id']);
    },
    'dj.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-client-secret']);
    },
    'dj.client_secret_zrc' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-client-secret-zrc']);
    },
    'dj.authenticator' => function (Container $container) {
        return $container->get(Clients\DecosJoin\Authenticator::class);
    },

    /**
     * RX.Mission configuration.
     */
    'rx-mission.abbr' => 'rx',
    'rx.enabled' => function (Container $container) {
        return (bool) $container->make('gf.setting', ['-suppliers-rx-mission-enabled']);
    },
    'rx.client' => fn (Container $container) => $container->get(Clients\RxMission\Client::class),
    'rx.catalogi_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-catalogi-url']);
    },
    'rx.documenten_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-documenten-url']);
    },
    'rx.zaken_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-zaken-url']);
    },
    'rx.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-client-id']);
    },
    'rx.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-client-secret']);
    },
    'rx.mijn_taken_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-mijn-taken-url']);
    },
    'rx.mijn_taken_api_key' => function (Container $container) {
        return $container->make('gf.setting', ['-rx-mission-mijn-taken-api-key']);
    },
    'rx.authenticator' => function (Container $container) {
        return $container->get(Clients\RxMission\Authenticator::class);
    },

    /**
     * Xxllnc configuration.
     */
    'xxllnc.abbr' => 'xxllnc',
    'xxllnc.enabled' => function (Container $container) {
        return (bool) $container->make('gf.setting', ['-suppliers-xxllnc-enabled']);
    },
    'xxllnc.client' => fn (Container $container) => $container->get(Clients\Xxllnc\Client::class),
    'xxllnc.catalogi_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-xxllnc-catalogi-url']);
    },
    'xxllnc.documenten_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-xxllnc-documenten-url']);
    },
    'xxllnc.zaken_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-xxllnc-zaken-url']);
    },
    'xxllnc.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-xxllnc-client-id']);
    },
    'xxllnc.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-xxllnc-client-secret']);
    },
    'xxllnc.mijn_taken_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-xxllnc-mijn-taken-url']);
    },
    'xxllnc.mijn_taken_api_key' => function (Container $container) {
        return $container->make('gf.setting', ['-xxllnc-mijn-taken-api-key']);
    },
    'xxllnc.authenticator' => function (Container $container) {
        return $container->get(Clients\Xxllnc\Authenticator::class);
    },

    /**
     * Procura configuration.
     */
    'procura.abbr' => 'procura',
    'procura.enabled' => function (Container $container) {
        return (bool) $container->make('gf.setting', ['-suppliers-procura-enabled']);
    },
    'procura.client' => fn (Container $container) => $container->get(Clients\Procura\Client::class),
    'procura.catalogi_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-procura-catalogi-url']);
    },
    'procura.documenten_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-procura-documenten-url']);
    },
    'procura.zaken_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-procura-zaken-url']);
    },
    'procura.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-procura-client-id']);
    },
    'procura.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-procura-client-secret']);
    },
    'procura.authenticator' => function (Container $container) {
        return $container->get(Clients\Procura\Authenticator::class);
    },

    /**
     * General configuration
     */
    'rsin' => function (Container $container) {
        return $container->make('gf.setting', ['-rsin']);
    },
    'zaak_image' => function (Container $container) {
        return $container->make('gf.setting', ['-zaak-image']);
    },
    'expand_enabled' => function (Container $container) {
        return (bool) $container->make('gf.setting', ['-zgw-expand']);
    },
    'expand_version' => function (Container $container) {
        switch ($container->make('gf.setting', ['-zgw-expand'])) {
            case '1':
                return '1.5.0';
            case '2':
                return '1.5.1';
        }
        return false;
    },

    /**
     * Utilize with $container->make('gf.setting', ['setting-name-here']);
     */
    'gf.setting' => function (Container $container, string $type, string $name) {
        return GravityForms\GravityFormsSettings::make()->get($name);
    },

    /**
     * Resolved BSN of logged in user.
     */
    'digid.current_user_bsn' => DigiDBsnResolver::make()->bsn(),

    /**
     * Configure API Clients
     */
    Clients\OpenZaak\Client::class => function (Container $container) {
        return new Clients\OpenZaak\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('oz.authenticator'),
            $container->get('oz.zaken_uri'),
            $container->get('oz.catalogi_uri'),
            $container->get('oz.documenten_uri'),
        );
    },

    Clients\DecosJoin\Client::class => function (Container $container) {
        return new Clients\DecosJoin\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('dj.authenticator'),
            $container->get('dj.zaken_uri'),
            $container->get('dj.catalogi_uri'),
            $container->get('dj.documenten_uri'),
        );
    },

    Clients\RxMission\Client::class => function (Container $container) {
        return new Clients\RxMission\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('rx.authenticator'),
            $container->get('rx.zaken_uri'),
            $container->get('rx.catalogi_uri'),
            $container->get('rx.documenten_uri'),
            $container->get('rx.mijn_taken_uri')
        );
    },

    Clients\Xxllnc\Client::class => function (Container $container) {
        return new Clients\Xxllnc\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class
            ),
            $container->get('xxllnc.authenticator'),
            $container->get('xxllnc.zaken_uri'),
            $container->get('xxllnc.catalogi_uri'),
            $container->get('xxllnc.documenten_uri'),
            $container->get('xxllnc.mijn_taken_uri')
        );
    },

    Clients\Procura\Client::class => function (Container $container) {
        return new Clients\Procura\Client(
            $container->make(
                Http\WordPress\WordPressRequestClient::class,
            )->applyCurlSslCertificates(),
            $container->get('procura.authenticator'),
            $container->get('procura.zaken_uri'),
            $container->get('procura.catalogi_uri'),
            $container->get('procura.documenten_uri'),
        );
    },

    /**
     * Authenticators
     */
    Clients\OpenZaak\Authenticator::class => function (Container $container) {
        return new Clients\OpenZaak\Authenticator(
            $container->get('oz.client_id'),
            $container->get('oz.client_secret'),
        );
    },

    Clients\DecosJoin\Authenticator::class => function (Container $container) {
        return new Clients\DecosJoin\Authenticator(
            $container->get('dj.client_id'),
            $container->get('dj.client_secret')
        );
    },

    Clients\RxMission\Authenticator::class => function (Container $container) {
        return new Clients\RxMission\Authenticator(
            $container->get('rx.client_id'),
            $container->get('rx.client_secret'),
            $container->get('rx.mijn_taken_api_key')
        );
    },

    Clients\Xxllnc\Authenticator::class => function (Container $container) {
        return new Clients\Xxllnc\Authenticator(
            $container->get('xxllnc.client_id'),
            $container->get('xxllnc.client_secret'),
            $container->get('xxllnc.mijn_taken_api_key')
        );
    },

    Clients\Procura\Authenticator::class => function (Container $container) {
        return new Clients\Procura\Authenticator(
            $container->get('procura.client_id'),
            $container->get('procura.client_secret')
        );
    },

    /**
     * HTTP clients
     */
    Http\WordPress\WordPressRequestClient::class => function (Container $container, string $type) {
        return new Http\WordPress\WordPressRequestClient(
            new Http\RequestOptions([
                'timeout' => 15,
                'headers' => [
                    'Accept-Crs' => 'EPSG:4326',
                    'Content-Crs' => 'EPSG:4326',
                    'Content-Type' => 'application/json',
                ],
            ])
        );
    },

    /**
     * HTTP Message logging
     */
    'message.logger.active' => false,
    'message.logger.detail' => Http\Logger\MessageDetail::BLACK_BOX,
    'message.logger.path' => dirname(ABSPATH) . '/owc-http-messages.json',
    'message.logger' => function (Container $container) {
        $logger = new \Monolog\Logger('owc_http_log');

        $handler = new \Monolog\Handler\StreamHandler(
            $container->get('message.logger.path'),
            \Monolog\Logger::DEBUG
        );

        $handler->setFormatter(new \Monolog\Formatter\JsonFormatter());
        $logger->pushHandler($handler);

        $logger->pushProcessor(new \Monolog\Processor\WebProcessor());
        $logger->pushProcessor(new Http\Logger\LogDetailProcessor($container->get('message.logger.detail')));
        $logger->pushProcessor(new Http\Logger\FilterBsnProcessor());

        return $logger;
    },
];
