<?php

declare(strict_types=1);

namespace JDecool\Clubhouse;

use Http\{
    Client\Common\HttpMethodsClient,
    Client\HttpClient,
    Discovery\Psr17FactoryDiscovery,
    Discovery\Psr18ClientDiscovery,
    Message\RequestFactory,
};
use RuntimeException;

class ClientBuilder
{
    public const V1 = 'v1';
    public const V2 = 'v2';
    public const V3 = 'v3';
    public const BETA = 'beta';

    private $httpClient;
    private $requestFactory;

    public function __construct(?HttpClient $httpClient = null, ?RequestFactory $requestFactory = null)
    {
        $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    }

    public function createClientV1(string $token): Client
    {
        return $this->create(self::V1, $token);
    }

    public function createClientV2(string $token): Client
    {
        return $this->create(self::V2, $token);
    }

    public function createClientV3(string $token): Client
    {
        return $this->create(self::V3, $token);
    }

    public function createClientBeta(string $token): Client
    {
        return $this->create(self::BETA, $token);
    }

    public function create(string $version, string $token): Client
    {
        $http = new HttpMethodsClient($this->httpClient, $this->requestFactory);

        switch ($version) {
            case self::V1:
                return Client::createV1($http, $token);

            case self::V2:
                return Client::createV2($http, $token);

            case self::V3:
                return Client::createV3($http, $token);

            case self::BETA:
                return Client::createBeta($http, $token);
        }

        throw new RuntimeException("Version '$version' is not supported.");
    }
}
