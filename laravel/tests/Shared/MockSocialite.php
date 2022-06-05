<?php

namespace Tests\Shared;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Laravel\Socialite\Contracts\Factory as Socialite;

trait MockSocialite
{
    public function mockSocialite(int $id, string $email, string $name): void
    {
        $socialiteUser = $this->createMock(\Laravel\Socialite\Two\User::class);
        $socialiteUser->id = $id;
        $socialiteUser->email = $email;
        $socialiteUser->name = $name;

        $socialiteUser->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $socialiteUser->expects($this->any())
            ->method('getName')
            ->willReturn($name);
        $socialiteUser->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);
        $socialiteUser->expects($this->any())
            ->method('getAvatar')
            ->willReturn('https://lh3.googleusercontent.com/a/AATXAJyjvc3Ab4YyUA-vI8hkMVwxX-RUAdzw-PWSYNRL=s96-c');

        $provider = $this->createMock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->expects($this->any())
            ->method('user')
            ->willReturn($socialiteUser);
        $provider->expects($this->any())
            ->method('stateless')
            ->willReturn($provider);

        $stub = $this->createMock(Socialite::class);
        $stub->expects($this->any())
            ->method('driver')
            ->willReturn($provider);

        // Replace Socialite Instance with our mock
        $this->app->instance(Socialite::class, $stub);
    }

    public function mockSocialiteException(): void
    {
        $provider = $this->createMock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->expects($this->any())
            ->method('user')
            ->willThrowException(new ClientException(
                'Socialite Exception',
                new Request('GET', 'https://testing.com'),
                new Response()
            ));
        $provider->expects($this->any())
            ->method('stateless')
            ->willReturn($provider);

        $stub = $this->createMock(Socialite::class);
        $stub->expects($this->any())
            ->method('driver')
            ->willReturn($provider);

        // Replace Socialite Instance with our mock
        $this->app->instance(Socialite::class, $stub);
    }
}
