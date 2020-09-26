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

class Client implements HttpClient
{
    private $http;

    public function __construct(HttpMethodsClientInterface $http)
    {
        $this->http = $http;
    }

    /**
     * @throws ClubhouseException
     */
    public function get(string $uri): array
    {
        $response = $this->http->get($uri);

        if (200 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws ClubhouseException
     * @throws \JsonException
     */
    public function post(string $uri, array $data): array
    {
        $response = $this->http->post($uri, [], json_encode($data, JSON_THROW_ON_ERROR));

        if (201 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws ClubhouseException
     * @throws \JsonException
     */
    public function put(string $uri, array $data): array
    {
        $response = $this->http->put($uri, [], json_encode($data, JSON_THROW_ON_ERROR));

        if (200 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws ClubhouseException
     */
    public function delete(string $uri): void
    {
        $response = $this->http->delete($uri);

        if (204 !== $response->getStatusCode()) {
            throw $this->createExceptionFromResponse($response);
        }
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
