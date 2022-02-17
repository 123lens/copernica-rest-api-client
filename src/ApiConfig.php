<?php
namespace Budgetlens\CopernicaRestApi;

use Budgetlens\CopernicaRestApi\Contracts\Config;

class ApiConfig implements Config
{
    private $middleware = [];

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
