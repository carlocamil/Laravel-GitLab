<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitLab.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\GitLab\Auth\Authenticators;

use Gitlab\Client;
use GrahamCampbell\GitLab\Auth\Authenticator\OauthAuthenticator;
use GrahamCampbell\Tests\GitLab\AbstractTestCase;
use InvalidArgumentException;
use Mockery;

/**
 * This is the oauth authenticator test class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class OauthAuthenticatorTest extends AbstractTestCase
{
    public function testMakeWithMethod(): void
    {
        $authenticator = new OauthAuthenticator();

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('authenticate')->once()
            ->with('your-token', 'oauth_token', null);

        $return = $authenticator->with($client)->authenticate([
            'token'  => 'your-token',
            'method' => 'token',
        ]);

        self::assertInstanceOf(Client::class, $return);
    }

    public function testMakeWithoutMethod(): void
    {
        $authenticator = new OauthAuthenticator();

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('authenticate')->once()
            ->with('your-token', 'oauth_token', null);

        $return = $authenticator->with($client)->authenticate([
            'token'  => 'your-token',
        ]);

        self::assertInstanceOf(Client::class, $return);
    }

    public function testMakeWithSudo(): void
    {
        $authenticator = new OauthAuthenticator();

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('authenticate')->once()
            ->with('your-token', 'oauth_token', 'foo');

        $return = $authenticator->with($client)->authenticate([
            'token'  => 'your-token',
            'method' => 'token',
            'sudo'   => 'foo',
        ]);

        self::assertInstanceOf(Client::class, $return);
    }

    public function testMakeWithoutToken(): void
    {
        $authenticator = new OauthAuthenticator();

        $client = Mockery::mock(Client::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The oauth authenticator requires a token.');

        $authenticator->with($client)->authenticate([]);
    }

    public function testMakeWithoutSettingClient(): void
    {
        $authenticator = new OauthAuthenticator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The client instance was not given to the authenticator.');

        $authenticator->authenticate([
            'token'  => 'your-token',
            'method' => 'token',
        ]);
    }
}
