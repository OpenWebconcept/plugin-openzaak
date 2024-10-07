<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\RxMission;

use Firebase\JWT\JWT;
use OWC\Zaaksysteem\Contracts\AbstractTokenAuthenticator;

class Authenticator extends AbstractTokenAuthenticator
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $mijnTakenApiKey;

    public function __construct(string $clientId, string $clientSecret, string $mijnTakenApiKey)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->mijnTakenApiKey = $mijnTakenApiKey;
    }

    public function generateToken(): string
    {
        $payload = [
            'iss' => $this->clientId,
            'iat' => time(),
            'client_id' => $this->clientId,
            'user_id' => $this->clientId,
            'user_representation' => $this->clientId,
        ];

        return JWT::encode($payload, $this->clientSecret, 'HS256');
    }
}
