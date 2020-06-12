<?php

declare(strict_types=1);

namespace JDecool\Clubhouse\Tests;

use JDecool\Clubhouse\{
    Client,
    ClientBuilder,
};
use RuntimeException;

test('create v1 client api', function(): void {
    $builder = new ClientBuilder();
    $client = $builder->createClientV1('foo');

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw when create v1 client api without token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->createClientV1('');
});

test('create v2 client api', function(): void {
    $builder = new ClientBuilder();
    $client = $builder->createClientV2('foo');

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw when create v2 client api without token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->createClientV2('');
});

test('create v3 client api', function(): void {
    $builder = new ClientBuilder();
    $client = $builder->createClientV3('foo');

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw when create v3 client api without token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->createClientV3('');
});

test('create beta client api', function(): void {
    $builder = new ClientBuilder();
    $client = $builder->createClientBeta('foo');

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw when create beta client api without token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->createClientBeta('');
});

test('create client', function($version): void {
    $builder = new ClientBuilder();
    $client = $builder->create($version, 'foo');

    $this->assertInstanceOf(Client::class, $client);
})->with([
    ClientBuilder::V1,
    ClientBuilder::V2,
    ClientBuilder::V3,
    ClientBuilder::BETA,
]);

test('an exception throw if trying to create a client with invalid version', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->create('foo', 'bar');
});

test('an exception thow when create client without api token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->create(ClientBuilder::V3, '');
});
