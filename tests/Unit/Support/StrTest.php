<?php
namespace Budgetlens\CopernicaRestApi\Tests\Support;

use Budgetlens\CopernicaRestApi\Support\Str;
use Budgetlens\CopernicaRestApi\Tests\TestCase;

class StrTest extends TestCase
{
    /** @test */
    public function studly()
    {
        $this->assertSame('LoremPHPIpsum', Str::studly('lorem_p_h_p_ipsum'));
        $this->assertSame('LoremPhpIpsum', Str::studly('lorem_php_ipsum'));
        $this->assertSame('LoremPhPIpsum', Str::studly('lorem-phP-ipsum'));
        $this->assertSame('LoremPhpIpsum', Str::studly('lorem  -_-  php   -_-   ipsum   '));

        $this->assertSame('FooBar', Str::studly('fooBar'));
        $this->assertSame('FooBar', Str::studly('foo_bar'));
        // test cache
        $this->assertSame('FooBar', Str::studly('foo_bar'));
        $this->assertSame('FooBarBaz', Str::studly('foo-barBaz'));
        $this->assertSame('FooBarBaz', Str::studly('foo-bar_baz'));
    }

    /** @test */
    public function upper()
    {
        $this->assertSame('FOO BAR BAZ', Str::upper('foo bar baz'));
        $this->assertSame('FOO BAR BAZ', Str::upper('foO bAr BaZ'));
    }
}
