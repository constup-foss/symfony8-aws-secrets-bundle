<?php

declare(strict_types = 1);

namespace ConstupFoss\Symfony8AwsSecretsBundle\Tests\Aws;

use ConstupFoss\Symfony8AwsSecretsBundle\Aws\AwsSecretsManagerClientFactory;
use ConstupFoss\Symfony8AwsSecretsBundle\Tests\Aws\DataProvider\AwsSecretsManagerClientFactory\CreateClientDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

class AwsSecretsManagerClientFactoryTest extends TestCase
{
    #[DataProviderExternal(
        CreateClientDataProvider::class,
        'provide_HappyFlow'
    )]
    public function test_createClient_HappyFlow(
        string $region,
        string $version,
        ?string $endpoint,
        ?string $profile,
        ?string $token,
        ?string $key,
        ?string $secret,
        array $expectedConfig,
    ): void {
        $result = AwsSecretsManagerClientFactory::createClient(
            $region,
            $version,
            $endpoint,
            $profile,
            $token,
            $key,
            $secret,
        );

        if ($endpoint !== null) {
            $this->assertEquals($expectedConfig['endpoint'], $result->getEndpoint());
        }
        if ($profile !== null) {
            $this->assertEquals($expectedConfig['profile'], $result->getConfig('profile'));
        }
        $this->assertEquals($expectedConfig['region'], $result->getConfig('signing_region'));
        if ($key !== null || $secret !== null || $token !== null) {
            $credentials = $result->getCredentials()->wait();
            $this->assertEquals($expectedConfig['credentials']['key'], $credentials->getAccessKeyId());
            $this->assertEquals($expectedConfig['credentials']['secret'], $credentials->getSecretKey());
            if ($token !== null) {
                $this->assertEquals($expectedConfig['credentials']['token'], $credentials->getSecurityToken());
            }
        }
    }
}
