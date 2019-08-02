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
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class ClientTest extends TestCase
{
    public function testCreateV1(): void
    {
        $client = Client::createV1(
            $this->createMock(HttpMethodsClient::class),
            'foo'
        );

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCreateV2(): void
    {
        $client = Client::createV2(
            $this->createMock(HttpMethodsClient::class),
            'foo'
        );

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCreateBeta(): void
    {
        $client = Client::createBeta(
            $this->createMock(HttpMethodsClient::class),
            'foo'
        );

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testContruct(): void
    {
        $client = new Client(
            $this->createMock(HttpMethodsClient::class),
            'http://domain.tld',
            'foo'
        );

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testConstructWithInvalidUrl(): void
    {
        $this->expectException(RuntimeException::class);

        new Client(
            $this->createMock(HttpMethodsClient::class),
            'foo',
            'bar'
        );
    }

    /**
     * @dataProvider httpMethods
     */
    public function testCallForV1(string $method, int $statusCode, $responseContent, ...$requestParams): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn($statusCode);
        $response->method('getBody')
            ->willReturn(json_encode($responseContent));

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method($method)
            ->with('https://api.clubhouse.io/api/v1/resource?token=foo')
            ->willReturn($response);

        $client = Client::createV1($http, 'foo');
        $resource = call_user_func([$client, $method], 'resource', $requestParams);

        $this->assertTrue(true, 'No error occured during call');
    }

    /**
     * @dataProvider httpMethods
     */
    public function testCallForV2(string $method, int $statusCode, $responseContent, ...$requestParams): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn($statusCode);
        $response->method('getBody')
            ->willReturn(json_encode($responseContent));

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method($method)
            ->with('https://api.clubhouse.io/api/v2/resource?token=foo')
            ->willReturn($response);

        $client = Client::createV2($http, 'foo');
        $resource = call_user_func([$client, $method], 'resource', $requestParams);

        $this->assertTrue(true, 'No error occured during call');
    }

    /**
     * @dataProvider httpMethods
     */
    public function testCallForBeta(string $method, int $statusCode, $responseContent, ...$requestParams): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn($statusCode);
        $response->method('getBody')
            ->willReturn(json_encode($responseContent));

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method($method)
            ->with('https://api.clubhouse.io/api/beta/resource?token=foo')
            ->willReturn($response);

        $client = Client::createBeta($http, 'foo');
        $resource = call_user_func([$client, $method], 'resource', $requestParams);

        $this->assertTrue(true, 'No error occured during call');
    }

    public function testGetCall(): void
    {
        $response = $this->createResponse(200, []);

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('get')
            ->with('http://domain.tld/resource/42?token=foo')
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');
        $resource = $client->get('resource/42');

        $this->assertIsArray($resource);
    }

    public function testGetCallWithError(): void
    {
        $response = $this->createResponse(400, ['message' => 'Bad request']);

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
    }

    public function testGetCallWithErrorWithDefaultMessage(): void
    {
        $response = $this->createResponse(500, '');

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
    }

    public function testPostCall(): void
    {
        $requestParams = [
            'foo' => 'bar',
            'qux' => 'quux',
        ];

        $response = $this->createResponse(201, []);

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('post')
            ->with(
                'http://domain.tld/resource?token=foo',
                ['Content-Type' => 'application/json'],
                json_encode($requestParams)
            )
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');
        $resource = $client->post('resource', $requestParams);

        $this->assertIsArray($resource);
    }

    public function testPostCallWithError(): void
    {
        $response = $this->createResponse(400, ['message' => 'Bad request']);

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('post')
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');

        $this->expectException(ClubhouseException::class);
        $this->expectException(LegacyClubhouseException::class);
        $this->expectExceptionMessage('Bad request');

        $client->post('resource', []);
    }

    public function testPostCallWithErrorWithDefaultMessage(): void
    {
        $response = $this->createResponse(500, '');

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('post')
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');

        $this->expectException(ClubhouseException::class);
        $this->expectException(LegacyClubhouseException::class);
        $this->expectExceptionMessage('An error occured.');

        $client->post('resource', []);
    }

    public function testPutCall(): void
    {
        $requestParams = [
            'foo' => 'bar',
            'qux' => 'quux',
        ];

        $response = $this->createResponse(200, []);

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('put')
            ->with(
                'http://domain.tld/resource/42?token=foo',
                ['Content-Type' => 'application/json'],
                json_encode($requestParams)
            )
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');
        $resource = $client->put('resource/42', $requestParams);

        $this->assertIsArray($resource);
    }

    public function testPutCallWithError(): void
    {
        $response = $this->createResponse(400, ['message' => 'Bad request']);

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('put')
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');

        $this->expectException(ClubhouseException::class);
        $this->expectException(LegacyClubhouseException::class);
        $this->expectExceptionMessage('Bad request');

        $client->put('resource/42', []);
    }

    public function testPutCallWithErrorWithDefaultMessage(): void
    {
        $response = $this->createResponse(500, '');

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('put')
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');

        $this->expectException(ClubhouseException::class);
        $this->expectException(LegacyClubhouseException::class);
        $this->expectExceptionMessage('An error occured.');

        $client->put('resource/42', []);
    }

    public function testDeleteCall(): void
    {
        $response = $this->createResponse(204, null);

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('delete')
            ->with('http://domain.tld/resource?token=foo')
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');
        $this->assertNull($client->delete('resource'));
    }

    public function testDeleteCallWithError(): void
    {
        $response = $this->createResponse(400, ['message' => 'Bad request']);

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('delete')
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');

        $this->expectException(ClubhouseException::class);
        $this->expectException(LegacyClubhouseException::class);
        $this->expectExceptionMessage('Bad request');

        $client->delete('resource/42', []);
    }

    public function testDeleteCallWithErrorWithDefaultMessage(): void
    {
        $response = $this->createResponse(500, '');

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method('delete')
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');

        $this->expectException(ClubhouseException::class);
        $this->expectException(LegacyClubhouseException::class);
        $this->expectExceptionMessage('An error occured.');

        $client->delete('resource/42', []);
    }

    /**
     * @dataProvider exceptionCalls
     */
    public function testCallsThrowAnException(string $method, int $statusCode, string $exceptionClass, ...$params): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn($statusCode);
        $response->method('getBody')
            ->willReturn(json_encode([]));

        $http = $this->createMock(HttpMethodsClient::class);
        $http->expects($this->once())
            ->method($method)
            ->willReturn($response);

        $client = new Client($http, 'http://domain.tld', 'foo');

        $this->expectException($exceptionClass);

        call_user_func([$client, $method], 'resource', $params);
    }

    public function exceptionCalls(): array
    {
        return [
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
        ];
    }

    public function httpMethods(): array
    {
        return [
            ['get', 200, []],
            ['post', 201, [], []],
            ['put', 200, [], []],
            ['delete', 204, null],
        ];
    }

    private function createResponse(int $statusCode, $content = null): ResponseInterface
    {
        $stream = new class($content) implements StreamInterface {
            private $content;

            public function __construct($content)
            {
                $this->content = json_encode($content);
            }

            public function __toString()
            {
                return (string) $this->content;
            }

            public function close()
            {
            }

            public function detach()
            {
            }

            public function getSize()
            {
            }

            public function tell()
            {
            }

            public function eof()
            {
            }

            public function isSeekable()
            {
            }

            public function seek($offset, $whence = SEEK_SET)
            {
            }

            public function rewind()
            {
            }

            public function isWritable()
            {
            }

            public function write($string)
            {
            }

            public function isReadable()
            {
            }

            public function read($length)
            {
            }

            public function getContents()
            {
                return $this->content;
            }

            public function getMetadata($key = null)
            {
            }
        };

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn($statusCode);
        $response->method('getBody')
            ->willReturn($stream);

        return $response;
    }
}
