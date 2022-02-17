# Copernica Rest Api Client

[Copernica API documentation](https://www.copernica.com/nl/documentation/restv3/rest-methods)

## Requirements

To use the Copernica Rest API client, the following things are required:

* Generate your [access_token](https://copernica.com)

## Installation

Install package using composer

``` bash
composer require budgetlens/copernica-rest-api-client
```

## Getting started

``` php
$accessToken = 'your-token';
$client = new \Budgetlens\CopernicaRestApi\Client($accessToken);
```
If required additional configuration can be added by supplying an additional config
``` php
class MyConfig implements Budgetlens\CopernicaRestApi\Contracts\Config
{
    public function getEndpoint(): string
    {
        return 'https://api.copernica.com/v3';
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function addMiddleware($middleware)
    {
        $this->middleware[] = $middleware;
    }

    public function getTimeout(): int
    {
        return 180;
    }

    public function getUserAgent(): string
    {
        return '';
    }
}
$client->setConfig($config);
```

# Examples
*for examples see "tests folder"*