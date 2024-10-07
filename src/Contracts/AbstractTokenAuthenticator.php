<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

abstract class AbstractTokenAuthenticator implements TokenAuthenticator
{
    protected string $clientSecret;
    protected string $mijnTakenApiKey;

    abstract public function generateToken(): string;

    /**
     * REFERENCE POINT: Mike -> This method is needed for the decos implementation.
     * 2 different api's are being used which both requires a different token for now?
     */
    public function setClientSecret(string $clientSecret): self
    {
        if (empty($clientSecret)) {
            return $this;
        }

        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function getAuthString(): string
    {
        return sprintf('Bearer %s', $this->generateToken());
    }

    public function getApiKeyMijnTaken(): string
    {
        return $this->mijnTakenApiKey;
    }
}
