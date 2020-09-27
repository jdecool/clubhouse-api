<?php

declare(strict_types=1);

namespace JDecool\Clubhouse;

use Http\{
    Client\Common\HttpMethodsClient,
    Client\Common\Plugin\AddHostPlugin,
    Client\Common\Plugin\AddPathPlugin,
    Client\Common\Plugin\AuthenticationPlugin,
    Client\Common\Plugin\HeaderSetPlugin,
    Client\Common\PluginClient,
    Client\HttpClient,
    Discovery\Psr17FactoryDiscovery,
    Discovery\Psr18ClientDiscovery,
    Message\Authentication\QueryParam,
    Message\RequestFactory,
};
use JDecool\Clubhouse\Exception\ClubhouseException;
use Psr\Http\Message\UriFactoryInterface;

class ClientBuilder
{
    private const ENDPOINT_V1 = 'https://api.clubhouse.io/api/v1';
    private const ENDPOINT_V2 = 'https://api.clubhouse.io/api/v2';
    private const ENDPOINT_V3 = 'https://api.clubhouse.io/api/v3';
    private const ENDPOINT_BETA = 'https://api.clubhouse.io/api/beta';

    private $httpClient;
    private $requestFactory;
    private $uriFactory;

    public function __construct(
        ?HttpClient $httpClient = null,
        ?RequestFactory $requestFactory = null,
        ?UriFactoryInterface $uriFactory = null
    ) {
        $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->uriFactory = $uriFactory ?? Psr17FactoryDiscovery::findUriFactory();
    }

    public function createClientV1(string $token): Client
    {
        return $this->create(self::ENDPOINT_V1, $token);
    }

    public function createClientV2(string $token): Client
    {
        return $this->create(self::ENDPOINT_V2, $token);
    }

    public function createClientV3(string $token): Client
    {
        return $this->create(self::ENDPOINT_V3, $token);
    }

    public function createClientBeta(string $token): Client
    {
        return $this->create(self::ENDPOINT_BETA, $token);
    }

    public function create(string $url, string $token): Client
    {
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new ClubhouseException('Invalid Clubouse base URI.');
        }

        if ('' === trim($token)) {
            throw new ClubhouseException('API token is required.');
        }

        $plugins = [
            new AuthenticationPlugin(
                new QueryParam(['token' => $token])
            ),
            new AddHostPlugin(
                $this->uriFactory->createUri($url)
            ),
            new AddPathPlugin(
                $this->uriFactory->createUri($url)
            ),
            new HeaderSetPlugin([
                'User-Agent' => 'github.com/jdecool/clubhouse-api',
                'Content-Type' => 'application/json',
            ])
        ];

        $http = new HttpMethodsClient(
            new PluginClient($this->httpClient, $plugins),
            $this->requestFactory
        );

        return new Client($http);
    }
}
