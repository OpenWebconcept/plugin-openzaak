<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http;

interface RequestClientInterface
{
    public function __construct(?RequestOptions $options = null);
    public function setRequestOptions(RequestOptions $options): self;
    public function getRequestOptions(): RequestOptions;
    public function get(string $url, ?RequestOptions $options = null): Response;
    public function post(string $url, $body, ?RequestOptions $options = null): Response;
    public function update(string $url, $body, ?RequestOptions $options = null): Response;
    public function delete(string $url, ?RequestOptions $options = null): Response;
}
