<?php

declare(strict_types = 1);

namespace ConstupFoss\Symfony8AwsSecretsBundle\Tests\Aws\DataProvider\AwsSecretsManagerClientFactory;

readonly class CreateClientDataProvider
{
    public static function provide_HappyFlow(): array
    {
        return [
            'Auth using profile.' => [
                'region' => $region = 'eu-central-1',
                'version' => $version = 'latest',
                'endpoint' => $endpoint = null,
                'profile' => $profile = 'test_profile',
                'token' => $token = null,
                'key' => $key = null,
                'secret' => $secret = null,
                'expectedConfig' => [
                    'region' => $region,
                    'version' => $version,
                    'profile' => $profile,
                ],
            ],
            'Auth using profile. Endpoint is used.' => [
                'region' => $region = 'eu-central-1',
                'version' => $version = 'latest',
                'endpoint' => $endpoint = 'test_endpoint',
                'profile' => $profile = 'test_profile',
                'token' => $token = null,
                'key' => $key = null,
                'secret' => $secret = null,
                'expectedConfig' => [
                    'region' => $region,
                    'version' => $version,
                    'profile' => $profile,
                    'endpoint' => $endpoint,
                ],
            ],
            'Auth using key and secret.' => [
                'region' => $region = 'eu-central-1',
                'version' => $version = 'latest',
                'endpoint' => $endpoint = null,
                'profile' => $profile = null,
                'token' => $token = null,
                'key' => $key = 'test_key',
                'secret' => $secret = 'test_secret',
                'expectedConfig' => [
                    'region' => $region,
                    'version' => $version,
                    'credentials' => [
                        'key' => $key,
                        'secret' => $secret,
                    ],
                ],
            ],
            'Auth using STS token.' => [
                'region' => $region = 'eu-central-1',
                'version' => $version = 'latest',
                'endpoint' => $endpoint = null,
                'profile' => $profile = null,
                'token' => $token = 'test_token',
                'key' => $key = 'test_key',
                'secret' => $secret = 'test_secret',
                'expectedConfig' => [
                    'region' => $region,
                    'version' => $version,
                    'credentials' => [
                        'key' => $key,
                        'secret' => $secret,
                        'token' => $token,
                    ],
                ],
            ],
        ];
    }
}
