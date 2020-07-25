<?php

declare(strict_types=1);

namespace JDecool\Clubhouse;

use JDecool\Clubhouse\{
    Exception\ClubhouseException,
    Exception\ResourceNotExist,
    Exception\SchemaMismatch,
    Exception\TooManyRequest,
    Exception\Unprocessable,
};
use Http\Client\Common\HttpMethodsClientInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Client implements HttpClient
{
    private const ENDPOINT_V1 = 'https://api.clubhouse.io/api/v1';
    private const ENDPOINT_V2 = 'https://api.clubhouse.io/api/v2';
    private const ENDPOINT_V3 = 'https://api.clubhouse.io/api/v3';
    private const ENDPOINT_BETA = 'https://api.clubhouse.io/api/beta';

    private $http;
    private $baseUri;
    private $token;

    public static function createV1(HttpMethodsClientInterface $http, string $token): self
    {
        return new self($http, self::ENDPOINT_V1, $token);
    }

    public static function createV2(HttpMethodsClientInterface $http, string $token): self
    {
        return new self($http, self::ENDPOINT_V2, $token);
    }

    public static function createV3(HttpMethodsClientInterface $http, string $token): self
    {
        return new self($http, self::ENDPOINT_V3, $token);
    }

    public static function createBeta(HttpMethodsClientInterface $http, string $token): self
    {
        return new self($http, self::ENDPOINT_BETA, $token);
    }

    public function __construct(HttpMethodsClientInterface $http, string $baseUri, string $token)
    {
        if (false === filter_var($baseUri, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Invalid Clubouse base URI.');
        }

        if ('' === trim($token)) {
            throw new RuntimeException('API token is required.');
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

        return json_decode($response->getBody()->getContents(), true);
    }

    public function post(string $uri, array $data): array
    {
        // @phpstan-ignore-next-line
        $response = $this->http->post(
            $this->endpoint($uri),
            ['Content-Type' => 'application/json'],
            json_encode($data)
        );

        if (201 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function put(string $uri, array $data): array
    {
        // @phpstan-ignore-next-line
        $response = $this->http->put(
            $this->endpoint($uri),
            ['Content-Type' => 'application/json'],
            json_encode($data)
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function delete(string $uri): void
    {
        $response = $this->http->delete(
            $this->endpoint($uri)
        );

        if (204 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }
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
        $content = json_decode($response->getBody()->getContents(), true);
        $message = $content['message'] ?? 'An error occured.';

        switch ($response->getStatusCode()) {
            case 400:
                return new SchemaMismatch($message);

            case 404:
                return new ResourceNotExist($message);

            case 422:
                return new Unprocessable($message);

            case 429:
                return new TooManyRequest($message);
        }

        return new ClubhouseException($message);
    }
}
