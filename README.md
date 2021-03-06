Clubhouse API client
====================

[![Build Status](https://img.shields.io/endpoint.svg?url=https%3A%2F%2Factions-badge.atrox.dev%2Fjdecool%2Fclubhouse-api%2Fbadge%3Fref%3Dmaster&style=flat)](https://actions-badge.atrox.dev/jdecool/clubhouse-api/goto?ref=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jdecool/clubhouse-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jdecool/clubhouse-api/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/jdecool/clubhouse-api/v/stable.png)](https://packagist.org/packages/jdecool/clubhouse-api)

A simple PHP client for [Clubhouse.io REST API](https://clubhouse.io/api/rest/v3/).

## Install it

The library is decoupled from any HTTP message client with [HTTPlug](http://httplug.io). That's why you need to install a client implementation `http://httplug.io/` in this example.

Install using [composer](https://getcomposer.org):

```bash
composer require jdecool/clubhouse-api guzzlehttp/guzzle http-interop/http-factory-guzzle
```

## Getting started

```php
<?php

require __DIR__.'/vendor/autoload.php';

$builder = new JDecool\Clubhouse\ClientBuilder();
$client = $builder->createClientV2('your-clubhouse-token'); // create client for Clubhouse API v2 (v1 is also available)

$story = $client->get('stories/144');
```

## LICENSE

This library is licensed under the MIT License.
