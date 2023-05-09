<?php

namespace OWC\Zaaksysteem;

use DI\Container;

/**
 * Link interfaces to their concretions.
 */

return [
    /**
     * OpenZaak configuration
     */
    'openzaak.abbr' => 'oz',
    'oz.client' => fn (Container $container) => $container->get(Client\OpenZaakClient::class),
    'oz.base_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-url']);
    },
    'oz.client_id' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-client-id']);
    },
    'oz.client_secret' => function (Container $container) {
        return $container->make('gf.setting', ['-openzaak-client-secret']);
    },
    'oz.authenticator' => function (Container $container) {
        return $container->get(Http\Authentication\OpenZaakAuthenticator::class);
    },

    /**
     * Decos JOIN configuration
     */
    'decosjoin.abbr' => 'dj',
    'dj.client' => fn (Container $container) => $container->get(Client\DecosJoinClient::class),
    'dj.base_uri' => function (Container $container) {
        return $container->make('gf.setting', ['-decos-join-url']);
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
    'dj.authenticator' => function (Container $container) {
        return $container->get(Http\Authentication\DecosJoinAuthenticator::class);
    },

    /**
     * General configuration
     */
    'rsin' => function (Container $container) {
        return $container->make('gf.setting', ['-rsin']);
    },

    /**
     * Utilize with $container->make('gf.setting', ['setting-name-here']);
     */
    'gf.setting' => function (Container $container, string $type, string $name) {
        return GravityForms\GravityFormsSettings::make()->get($name);
    },

    /**
     * Configure API Clients
     */
    Client\OpenZaakClient::class => function (Container $container) {
        return new Client\OpenZaakClient(
            $container->make(
                Http\WordPress\WordPressRequestClient::class,
                [$container->get('oz.base_uri')]
            ),
            $container->get('oz.authenticator'),
        );
    },
    Client\DecosJoinClient::class => function (Container $container) {
        return new Client\DecosJoinClient(
            $container->make(
                Http\WordPress\WordPressRequestClient::class,
                [$container->get('dj.base_uri')]
            ),
            $container->get('dj.authenticator'),
        );
    },

    /**
     * Authenticators
     */
    Http\Authentication\OpenZaakAuthenticator::class => function (Container $container) {
        return new Http\Authentication\OpenZaakAuthenticator(
            $container->get('oz.client_id'),
            $container->get('oz.client_secret'),
        );
    },
    Http\Authentication\DecosJoinAuthenticator::class => function (Container $container) {
        return new Http\Authentication\DecosJoinAuthenticator(
            $container->get('http.client'),
            $container->get('dj.token_uri'),
            $container->get('dj.client_id'),
            $container->get('dj.client_secret')
        );
    },

    /**
     * HTTP clients
     */
    Http\WordPress\WordPressRequestClient::class => function (Container $container, string $type, string $baseUri) {
        return new Http\WordPress\WordPressRequestClient(
            new Http\RequestOptions([
                'base_uri'      => $baseUri,
                'headers'       => [
                    'Accept-Crs'    => 'EPSG:4326',
                    'Content-Crs'   => 'EPSG:4326',
                    'Content-Type'  => 'application/json',
                ]
            ])
        );
    },
];
