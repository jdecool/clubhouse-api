<?php

declare(strict_types=1);

namespace JDecool\Clubhouse;

use Http\{
    Client\Common\HttpMethodsClient,
    Client\HttpClient,
    Discovery\HttpClientDiscovery,
    Discovery\MessageFactoryDiscovery,
    Message\MessageFactory
};
use RuntimeException;

class ClientBuilder
{
    public const V1 = 'v1';
    public const V2 = 'v2';

    private $httpClient;
    private $messageFactory;

    public function __construct(?HttpClient $httpClient = null, ?MessageFactory $messageFactory = null)
    {
        $this->httpClient = $httpClient ?? HttpClientDiscovery::find();
        $this->messageFactory = $messageFactory ?? MessageFactoryDiscovery::find();
    }

    public function createClientV1(string $token): Client
    {
        return $this->create(self::V1, $token);
    }

    public function createClientV2(string $token): Client
    {
        return $this->create(self::V2, $token);
    }

    public function create(string $version, string $token): Client
    {
        $http = new HttpMethodsClient($this->httpClient, $this->messageFactory);

        switch ($version) {
            case self::V1:
                return Client::createV1($http, $token);

            case self::V2:
                return Client::createV2($http, $token);
        }

        throw new RuntimeException("Version '$version' is not supported.");
    }
}
