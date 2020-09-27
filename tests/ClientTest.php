<?php

declare(strict_types=1);

namespace JDecool\Clubhouse\Tests;

use JDecool\Clubhouse\{
    Client,
    Exception\ClubhouseException,
    Exception\ResourceNotExist,
    Exception\SchemaMismatch,
    Exception\TooManyRequest,
    Exception\Unprocessable,
    HttpClient,
};
use Http\Client\Common\HttpMethodsClientInterface;
use Psr\Http\Message\{
    ResponseInterface,
    StreamInterface,
};

test('create api client', function(): void {
    $client = new Client($this->createMock(HttpMethodsClientInterface::class));

    $this->assertInstanceOf(HttpClient::class, $client);
    $this->assertInstanceOf(Client::class, $client);
});

test('get call', function(): void {
    $response = $this->createMock(ResponseInterface::class);
    $response->method('getStatusCode')
        ->willReturn(200);
    $response->method('getBody')
        ->willReturn($stream = $this->createMock(StreamInterface::class));

    $stream->method('getContents')
        ->willReturn(json_encode([]));

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('get')
        ->with('resource/42')
        ->willReturn($response);

    $client = new Client($http);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('get')
        ->with('resource/42')
        ->willReturn($response);

    $client = new Client($http);

    $this->expectException(ClubhouseException::class);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('get')
        ->with('resource/42')
        ->willReturn($response);

    $client = new Client($http);

    $this->expectException(ClubhouseException::class);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('post')
        ->with(
            'resource',
            [],
            json_encode($requestParams),
        )
        ->willReturn($response);

    $client = new Client($http);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('post')
        ->willReturn($response);

    $client = new Client($http);

    $this->expectException(ClubhouseException::class);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('post')
        ->willReturn($response);

    $client = new Client($http);

    $this->expectException(ClubhouseException::class);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('put')
        ->with(
            'resource',
            [],
            json_encode($requestParams),
        )
        ->willReturn($response);

    $client = new Client($http);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('put')
        ->willReturn($response);

    $client = new Client($http);

    $this->expectException(ClubhouseException::class);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('put')
        ->willReturn($response);

    $client = new Client($http);

    $this->expectException(ClubhouseException::class);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('delete')
        ->with('resource')
        ->willReturn($response);

    $client = new Client($http);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('delete')
        ->willReturn($response);

    $client = new Client($http);

    $this->expectException(ClubhouseException::class);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method('delete')
        ->willReturn($response);

    $client = new Client($http);

    $this->expectException(ClubhouseException::class);
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

    $http = $this->createMock(HttpMethodsClientInterface::class);
    $http->expects($this->once())
        ->method($method)
        ->willReturn($response);

    $client = new Client($http);

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
