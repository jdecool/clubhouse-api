<?php

declare(strict_types=1);

namespace JDecool\Clubhouse\Tests;

use JDecool\{
    Clubhouse\Client,
    Clubhouse\ClientBuilder
};
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ClientBuilderTest extends TestCase
{
    public function testCreateClientV1(): void
    {
        $builder = new ClientBuilder();
        $client = $builder->createClientV1('foo');

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCreateClientV2(): void
    {
        $builder = new ClientBuilder();
        $client = $builder->createClientV2('foo');

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCreateClientV3(): void
    {
        $builder = new ClientBuilder();
        $client = $builder->createClientV3('foo');

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCreateClientBeta(): void
    {
        $builder = new ClientBuilder();
        $client = $builder->createClientBeta('foo');

        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @dataProvider versions
     */
    public function testCreateClient(string $version): void
    {
        $builder = new ClientBuilder();
        $client = $builder->create($version, 'foo');

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCreateClientWithInvalidVersion(): void
    {
        $builder = new ClientBuilder();

        $this->expectException(RuntimeException::class);

        $builder->create('foo', 'bar');
    }

    public function versions(): array
    {
        return [
            [ClientBuilder::V1],
            [ClientBuilder::V2],
            [ClientBuilder::V3],
            [ClientBuilder::BETA],
        ];
    }
}
