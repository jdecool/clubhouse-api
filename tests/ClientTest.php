<?php

declare(strict_types=1);

namespace JDecool\Clubhouse\Tests;

use Http\Client\Common\HttpMethodsClient;
use JDecool\Clubhouse\{
    Client,
    ClubhouseException as LegacyClubhouseException,
    Exception\ClubhouseException,
    Exception\ResourceNotExist,
    Exception\SchemaMismatch,
    Exception\TooManyRequest,
    Exception\Unprocessable
};
use Psr\Http\Message\{
    ResponseInterface,
    StreamInterface
};
use RuntimeException;

test('create v1 api client', function(): void {
    $client = Client::createV1(
        $this->createMock(HttpMethodsClient::class),
        'foo',
    );

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw  when create v1 api client whithout api token', function(): void {
    $this->expectException(RuntimeException::class);

    Client::createV1(
        $this->createMock(HttpMethodsClient::class),
        '',
    );
});

test('create v2 api client', function(): void {
    $client = Client::createV2(
        $this->createMock(HttpMethodsClient::class),
        'foo',
    );

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw  when create v2 api client whithout api token', function(): void {
    $this->expectException(RuntimeException::class);

    Client::createV2(
        $this->createMock(HttpMethodsClient::class),
        '',
    );
});

test('create v3 api client', function(): void {
    $client = Client::createV3(
        $this->createMock(HttpMethodsClient::class),
        'foo',
    );

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw  when create v3 api client whithout api token', function(): void {
    $this->expectException(RuntimeException::class);

    Client::createV1(
        $this->createMock(HttpMethodsClient::class),
        '',
    );
});

test('create beta api client', function(): void {
    $client = Client::createBeta(
        $this->createMock(HttpMethodsClient::class),
        'foo',
    );

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw  when create beta api client whithout api token', function(): void {
    $this->expectException(RuntimeException::class);

    Client::createBeta(
        $this->createMock(HttpMethodsClient::class),
        '',
    );
});

test('create client through constructor', function(): void {
    $client = new Client(
        $this->createMock(HttpMethodsClient::class),
        'http://domain.tld',
        'foo',
    );

    $this->assertInstanceOf(Client::class, $client);
});

test('an exception throw when create client with invalid url', function(): void {
    $this->expectException(RuntimeException::class);

    new Client(
        $this->createMock(HttpMethodsClient::class),
        'foo',
        'bar',
    );
});

test('en exception throw when create client without api token', function(): void {
    $this->expectException(RuntimeException::class);

    new Client(
        $this->createMock(HttpMethodsClient::class),
        'http://domain.tld',
        '',
    );
});

test('v1 api client call', function(string $method, int $statusCode, $responseContent, ...$requestParams): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn($statusCode);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode($responseContent));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method($method)
        ->with('https://api.clubhouse.io/api/v1/resource?token=foo')
        ->willReturn($response);

    $client = Client::createV1($http, 'foo');
    $resource = call_user_func([$client, $method], 'resource', $requestParams);

    $this->assertTrue(true, 'No error occured during call');
})->with('http methods');

test('v2 api client call', function(string $method, int $statusCode, $responseContent, ...$requestParams): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn($statusCode);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode($responseContent));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method($method)
        ->with('https://api.clubhouse.io/api/v2/resource?token=foo')
        ->willReturn($response);

    $client = Client::createV2($http, 'foo');
    $resource = call_user_func([$client, $method], 'resource', $requestParams);

    $this->assertTrue(true, 'No error occured during call');
})->with('http methods');

test('v3 api client call', function(string $method, int $statusCode, $responseContent, ...$requestParams): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn($statusCode);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode($responseContent));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method($method)
        ->with('https://api.clubhouse.io/api/v3/resource?token=foo')
        ->willReturn($response);

    $client = Client::createV3($http, 'foo');
    $resource = call_user_func([$client, $method], 'resource', $requestParams);

    $this->assertTrue(true, 'No error occured during call');
})->with('http methods');

test('beta api client call', function(string $method, int $statusCode, $responseContent, ...$requestParams): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn($statusCode);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode($responseContent));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method($method)
        ->with('https://api.clubhouse.io/api/beta/resource?token=foo')
        ->willReturn($response);

    $client = Client::createBeta($http, 'foo');
    $resource = call_user_func([$client, $method], 'resource', $requestParams);

    $this->assertTrue(true, 'No error occured during call');
})->with('http methods');

test('get call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(200);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode([]));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('get')
        ->with('http://domain.tld/resource/42?token=foo')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');
    $resource = $client->get('resource/42');

    $this->assertIsArray($resource);
});

test('an exception throw on error in get call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(400);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(['message' => 'Bad request']));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('get')
        ->with('http://domain.tld/resource/42?token=foo')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');

    $this->expectException(ClubhouseException::class);
    $this->expectException(LegacyClubhouseException::class);
    $this->expectExceptionMessage('Bad request');

    $client->get('resource/42');
});

test('an exception throw with default message on error in get call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(500);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(null));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('get')
        ->with('http://domain.tld/resource/42?token=foo')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');

    $this->expectException(ClubhouseException::class);
    $this->expectException(LegacyClubhouseException::class);
    $this->expectExceptionMessage('An error occured.');

    $client->get('resource/42');
});

test('api post call', function(): void {
    $requestParams = [
        'foo' => 'bar',
        'qux' => 'quux',
    ];

    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(201);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode([]));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('post')
        ->with(
            'http://domain.tld/resource?token=foo',
            ['Content-Type' => 'application/json'],
            json_encode($requestParams),
        )
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');
    $resource = $client->post('resource', $requestParams);

    $this->assertIsArray($resource);
});

test('an exception throw on error in api post call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(400);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(['message' => 'Bad request']));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('post')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');

    $this->expectException(ClubhouseException::class);
    $this->expectException(LegacyClubhouseException::class);
    $this->expectExceptionMessage('Bad request');

    $client->post('resource', []);
});

test('an exception throw with default message on error in api post call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(500);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(''));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('post')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');

    $this->expectException(ClubhouseException::class);
    $this->expectException(LegacyClubhouseException::class);
    $this->expectExceptionMessage('An error occured.');

    $client->post('resource', []);
});

test('api put call', function(): void {
    $requestParams = [
        'foo' => 'bar',
        'qux' => 'quux',
    ];

    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(200);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode([]));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('put')
        ->with(
            'http://domain.tld/resource?token=foo',
            ['Content-Type' => 'application/json'],
            json_encode($requestParams),
        )
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');
    $resource = $client->put('resource', $requestParams);

    $this->assertIsArray($resource);
});

test('an exception throw on error in api put call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(400);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(['message' => 'Bad request']));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('put')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');

    $this->expectException(ClubhouseException::class);
    $this->expectException(LegacyClubhouseException::class);
    $this->expectExceptionMessage('Bad request');

    $client->put('resource', []);
});

test('an exception throw with default message on error in api put call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(500);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(''));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('put')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');

    $this->expectException(ClubhouseException::class);
    $this->expectException(LegacyClubhouseException::class);
    $this->expectExceptionMessage('An error occured.');

    $client->put('resource', []);
});

test('api delete call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(204);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(null));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('delete')
        ->with('http://domain.tld/resource?token=foo')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');
    $client->delete('resource');
});

test('an exception throw on error in api delete call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(400);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(['message' => 'Bad request']));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('delete')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');

    $this->expectException(ClubhouseException::class);
    $this->expectException(LegacyClubhouseException::class);
    $this->expectExceptionMessage('Bad request');

    $client->delete('resource');
});

test('an exception throw with default message on error in api delete call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(500);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(''));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method('delete')
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');

    $this->expectException(ClubhouseException::class);
    $this->expectException(LegacyClubhouseException::class);
    $this->expectExceptionMessage('An error occured.');

    $client->delete('resource');
});

test('an exception throw on api error', function(string $method, int $statusCode, string $exceptionClass, ...$params): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn($statusCode);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode(null));

    $http = $this->createMock(HttpMethodsClient::class);
    $http->expects($this->once())
        ->method($method)
        ->willReturn($response);

    $client = new Client($http, 'http://domain.tld', 'foo');

    $this->expectException($exceptionClass);

    call_user_func([$client, $method], 'resource', $params);
})->with([
    ['get', 404, ResourceNotExist::class],
    ['post', 404, ResourceNotExist::class, []],
    ['put', 404, ResourceNotExist::class, []],
    ['delete', 404, ResourceNotExist::class],

    ['get', 400, SchemaMismatch::class],
    ['post', 400, SchemaMismatch::class, []],
    ['put', 400, SchemaMismatch::class, []],
    ['delete', 400, SchemaMismatch::class],

    ['get', 429, TooManyRequest::class],
    ['post', 429, TooManyRequest::class, []],
    ['put', 429, TooManyRequest::class, []],
    ['delete', 429, TooManyRequest::class],

    ['get', 422, Unprocessable::class],
    ['post', 422, Unprocessable::class, []],
    ['put', 422, Unprocessable::class, []],
    ['delete', 422, Unprocessable::class],
]);

dataset('http methods', [
    ['get', 200, []],
    ['post', 201, [], []],
    ['put', 200, [], []],
    ['delete', 204, null],
]);
