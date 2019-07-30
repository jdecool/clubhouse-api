<?php

declare(strict_types=1);

namespace JDecool\Clubhouse;

use Http\Client\Common\HttpMethodsClient;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Client
{
    private const ENDPOINT_V1 = 'https://api.clubhouse.io/api/v1';
    private const ENDPOINT_V2 = 'https://api.clubhouse.io/api/v2';
    private const ENDPOINT_BETA = 'https://api.clubhouse.io/api/beta';

    private $http;
    private $baseUri;
    private $token;

    public static function createV1(HttpMethodsClient $http, string $token): self
    {
        return new self($http, self::ENDPOINT_V1, $token);
    }

    public static function createV2(HttpMethodsClient $http, string $token): self
    {
        return new self($http, self::ENDPOINT_V2, $token);
    }

    public static function createBeta(HttpMethodsClient $http, string $token): self
    {
        return new self($http, self::ENDPOINT_BETA, $token);
    }

    public function __construct(HttpMethodsClient $http, string $baseUri, string $token)
    {
        if (false === filter_var($baseUri, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Invalid Clubouse base URI.');
        }

        $this->http = $http;
        $this->baseUri = $baseUri;
        $this->token = $token;
    }

    public function get(string $uri): array
    {
        $response = $this->http->get(
            $this->endpoint($uri)
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }

        return json_decode((string) $response->getBody(), true);
    }

    public function post(string $uri, array $data): array
    {
        $response = $this->http->post(
            $this->endpoint($uri),
            ['Content-Type' => 'application/json'],
            json_encode($data)
        );

        if (201 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }

        return json_decode((string) $response->getBody(), true);
    }

    public function put(string $uri, array $data): array
    {
        $response = $this->http->put(
            $this->endpoint($uri),
            ['Content-Type' => 'application/json'],
            json_encode($data)
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }

        return json_decode((string) $response->getBody(), true);
    }

    public function delete(string $uri): array
    {
        $response = $this->http->delete(
            $this->endpoint($uri)
        );

        if (204 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }

        return json_decode((string) $response->getBody(), true);
    }

    private function endpoint(string $uri): string
    {
        return sprintf(
            '%s/%s?token=%s',
            rtrim($this->baseUri, '/'),
            ltrim($uri, '/'),
            $this->token
        );
    }

    private function createExceptionFromResponse(ResponseInterface $response): ClubhouseException
    {
        $content = json_decode((string) $response->getBody(), true);

        return new ClubhouseException($content['message'] ?? 'An error occured.');
    }
}
