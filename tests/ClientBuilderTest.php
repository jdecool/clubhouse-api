<?php

declare(strict_types=1);

namespace JDecool\Clubhouse\Tests;

use JDecool\Clubhouse\{
    Client,
    ClientBuilder,
};
use RuntimeException;

test('create v1 api client', function(): void {
    $builder = new ClientBuilder();
    $client = $builder->createClientV1('foo');

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw when create v1 api client without token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->createClientV1('');
});

test('create v2 api client', function(): void {
    $builder = new ClientBuilder();
    $client = $builder->createClientV2('foo');

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw when create v2 api client without token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->createClientV2('');
});

test('create v3 api client', function(): void {
    $builder = new ClientBuilder();
    $client = $builder->createClientV3('foo');

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw when create v3 api client without token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->createClientV3('');
});

test('create beta api client', function(): void {
    $builder = new ClientBuilder();
    $client = $builder->createClientBeta('foo');

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw when create beta api client without token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->createClientBeta('');
});

test('create client', function(): void {
    $builder = new ClientBuilder();
    $client = $builder->create('https://domain.tld/api', 'foo');

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw if trying to create a client with invalid version', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->create('foo', 'bar');
});

test('an exception thow when create client without api token', function(): void {
    $builder = new ClientBuilder();

    $this->expectException(RuntimeException::class);

    $builder->create('http://domain.tld/api', '');
});
