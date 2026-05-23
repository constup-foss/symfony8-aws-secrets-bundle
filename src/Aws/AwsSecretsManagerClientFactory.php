<?php

declare(strict_types = 1);

namespace ConstupFoss\Symfony8AwsSecretsBundle\Aws;

use Aws\SecretsManager\SecretsManagerClient;

class AwsSecretsManagerClientFactory
{
    /**
     * @param string      $region
     * @param string      $version
     * @param string|null $endpoint
     * @param null|string $profile
     * @param null|string $token
     * @param null|string $key
     * @param null|string $secret
     *
     * @return SecretsManagerClient
     */
    public static function createClient(
        string $region,
        string $version,
        ?string $endpoint,
        ?string $profile,
        ?string $token,
        ?string $key,
        ?string $secret
    ): SecretsManagerClient {
        $config = [
            'region' => $region,
            'version' => $version,
        ];

        if ($endpoint) {
            $config['endpoint'] = $endpoint;
        }

        if ($profile !== null && $profile !== '') {
            // use a profile for authentication, for example, IAM Roles Anywhere profile
            $config['profile'] = $profile;
        }

        if ($key !== null && $key !== '' && $secret !== null && $secret !== '') {
            // Use Access Key and Secret for authentication
            $config['credentials'] = [
                'key' => $key,
                'secret' => $secret,
            ];

            if ($token !== null && $token !== '') {
                // Use STS token for temporary credentials
                $config['credentials']['token'] = $token;
            }
        }

        return new SecretsManagerClient($config);
    }
}
