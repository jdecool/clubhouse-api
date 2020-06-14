<?php

declare(strict_types=1);

namespace JDecool\Clubhouse;

use JDecool\Clubhouse\Exception\ClubhouseException;

interface HttpClient
{
    /**
     * @throws ClubhouseException
     */
    public function get(string $uri): array;

    /**
     * @throws ClubhouseException
     */
    public function post(string $uri, array $data): array;

    /**
     * @throws ClubhouseException
     */
    public function put(string $uri, array $data): array;

    /**
     * @throws ClubhouseException
     */
    public function delete(string $uri): void;
}
