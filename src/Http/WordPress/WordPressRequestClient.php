<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\WordPress;

use InvalidArgumentException;
use OWC\Zaaksysteem\Http\RequestClientInterface;
use OWC\Zaaksysteem\Http\RequestOptions;
use OWC\Zaaksysteem\Http\Response;

use function Yard\DigiD\Foundation\Helpers\config;

class WordPressRequestClient implements RequestClientInterface
{
    protected RequestOptions $options;

    public function __construct(?RequestOptions $options = null)
    {
        $this->options = $options ?: new RequestOptions([]);
        $this->conditionallyDisableSslVerification();
    }

    public function applyCurlSslCertificates(): self
    {
        $sslPublicCert = config('digid.certificate.public');
        $sslPrivateCert = config('digid.certificate.private');

        if (empty($sslPublicCert) || empty($sslPrivateCert)) {
            throw new InvalidArgumentException('Missing SSL certificates: both public and private certificates are required for WordPressRequestClient configuration.');
        }

        add_action('http_api_curl', function ($handle) use ($sslPublicCert, $sslPrivateCert) {
            curl_setopt($handle, CURLOPT_SSLCERT, $sslPublicCert);
            curl_setopt($handle, CURLOPT_SSLKEY, $sslPrivateCert);
        });

        return $this;
    }

    /**
     * Some 'zaaksystemen' require SSL verification to be disabled since they are using ip-addresses insteaf of a domain.
     */
    private function conditionallyDisableSslVerification(): void
    {
        $shouldDisable = (bool) apply_filters('owc_gravityforms_zaaksysteem_disable_ssl_verification', false);

        if ($shouldDisable) {
            add_filter('https_ssl_verify', '__return_false');
        }
    }

    public function setRequestOptions(RequestOptions $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getRequestOptions(): RequestOptions
    {
        return $this->options;
    }

    public function get(string $uri, ?RequestOptions $options = null): Response
    {
        $response = wp_remote_get(
            $this->buildUri($uri),
            $this->mergeRequestOptions($options)->toArray()
        );

        return $this->handleResponse($response);
    }

    public function post(string $uri, $body, ?RequestOptions $options = null): Response
    {
        $options = $this->mergeRequestOptions($options)->set('body', $body);
        $response = wp_remote_post($this->buildUri($uri), $options->toArray());

        return $this->handleResponse($response);
    }

    public function update(string $uri, $body, ?RequestOptions $options = null): Response
    {
        $options = $this->mergeRequestOptions($options);
        $options->set('body', json_encode($body));
        $options->set('method', 'PATCH');

        $response = wp_remote_request(
            $uri,
            $options->toArray()
        );

        return $this->handleResponse($response);
    }

    public function delete(string $uri, ?RequestOptions $options = null): Response
    {
        $options->set('method', 'DELETE');

        $response = wp_remote_request(
            $this->buildUri($uri),
            $this->mergeRequestOptions($options)->toArray()
        );

        return $this->handleResponse($response);
    }

    protected function handleResponse($response): Response
    {
        if (is_wp_error($response)) {
            throw WordPressRequestError::fromWpError($response);
        }

        return WordPressClientResponse::fromResponse($response);
    }

    protected function mergeRequestOptions(?RequestOptions $options = null): RequestOptions
    {
        $this->options->addHeader('_owc_request_logging', microtime(true));

        if (! $options) {
            return $this->options;
        }

        return $this->options->clone()->merge($options);
    }

    protected function buildUri($uri): string
    {
        if ($this->options->has('base_uri')) {
            $uri = rtrim($this->options->get('base_uri'), '/') . '/' . $uri;
        }

        return $uri;
    }
}
