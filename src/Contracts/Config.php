<?php
namespace Budgetlens\CopernicaRestApi\Contracts;

interface Config
{
    public function getEndpoint(): string;
    public function getMiddleware(): array;
    public function getTimeout(): int;
    public function getUserAgent(): string;
}
