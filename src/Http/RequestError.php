<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http;

use Exception;
use Throwable;

class RequestError extends Exception
{
    protected ?Response $response = null;

    public static function fromResponse(Response $response)
    {
        try {
            $json = $response->getParsedJson();
            $message = (new static())->formatResponse($json);
            $status = $json['status'] ?? 0;
        } catch (Throwable $e) {
            $message = 'A request error occurred. Additionally, no error message could be retrieved.';
            $status = 0;
        }

        $error = new static($message, $status);
        $error->setResponse($response);

        return $error;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    protected function formatResponse(array $json): string
    {
        if (isset($json['title']) && isset($json['detail'])) {
            return sprintf('%s "%s".', $json['title'], $json['detail']);
        }

        if (isset($json['message']) && isset($json['exception'])) {
            return sprintf('%s "%s".', $json['message'], $json['exception']);
        }

        return 'A request error occurred. Additionally, no error message could be retrieved.';
    }
}
