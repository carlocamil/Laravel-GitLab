<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitLab.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\GitLab;

use Gitlab\Client;
use GrahamCampbell\GitLab\Authenticators\AuthenticatorFactory;
use GrahamCampbell\GitLab\GitLabFactory;
use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use Http\Client\Common\HttpMethodsClient;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use InvalidArgumentException;
use Mockery;

/**
 * This is the gitlab factory test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class GitLabFactoryTest extends AbstractTestBenchTestCase
{
    public function testMakeStandard()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'token']);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public function testMakeStandardNoCacheFactory()
    {
        $factory = $this->getFactory(false);

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'token']);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public function testMakeStandardExplicitCache()
    {
        $factory = $this->getFactory();

        $factory[1]->shouldReceive('store')->once()->with(null)->andReturn(Mockery::mock(Repository::class));

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'token', 'cache' => true]);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public function testMakeStandardExplicitCacheNoCacheFactory()
    {
        $factory = $this->getFactory(false);

        $factory[1]->shouldReceive('store')->once()->with(null)->andReturn(Mockery::mock(Repository::class));

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'token', 'cache' => true]);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public function testMakeStandardNamedCache()
    {
        $factory = $this->getFactory();

        $factory[1]->shouldReceive('store')->once()->with('foo')->andReturn(Mockery::mock(Repository::class));

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'token', 'cache' => 'foo']);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public function testMakeStandardNamedCacheNoCacheFactory()
    {
        $factory = $this->getFactory(false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Caching support not available.');

        $factory[0]->make(['token' => 'your-token', 'method' => 'token', 'cache' => 'foo']);
    }

    public function testMakeStandardNoCacheOrBackoff()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'token', 'cache' => false, 'backoff' => false]);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public function testMakeStandardNoCacheOrBackoffNoCacheFactory()
    {
        $factory = $this->getFactory(false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Caching support not available.');

        $factory[0]->make(['token' => 'your-token', 'method' => 'token', 'cache' => false, 'backoff' => false]);
    }

    public function testMakeStandardExplicitBackoff()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'token', 'backoff' => true]);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public function testMakeStandardExplicitUrl()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'token', 'url' => 'https://api.example.com']);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public function testMakeNoneMethod()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['method' => 'none']);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public function testMakeInvalidMethod()
    {
        $factory = $this->getFactory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported authentication method [bar].');

        $factory[0]->make(['method' => 'bar']);
    }

    public function testMakeEmpty()
    {
        $factory = $this->getFactory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The gitlab factory requires an auth method.');

        $factory[0]->make([]);
    }

    protected function getFactory(bool $cache = true)
    {
        $cache = $cache ? Mockery::mock(Factory::class) : null;

        return [new GitLabFactory(new AuthenticatorFactory(), $cache), $cache];
    }
}
