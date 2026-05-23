<?php

declare(strict_types = 1);

namespace ConstupFoss\Symfony8AwsSecretsBundle\Aws;

use Aws\SecretsManager\SecretsManagerClient;

readonly class EnvVarProvider
{
    const string AWS_SECRET_ID = 'SecretId';
    const string AWS_SECRET_STRING = 'SecretString';

    /**
     * @param SecretsManagerClient $secretsManagerClient
     */
    public function __construct(
        private SecretsManagerClient $secretsManagerClient
    ) {
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function get(string $name): string
    {
        return $this->secretsManagerClient
            ->getSecretValue([self::AWS_SECRET_ID => $name])
            ->get(self::AWS_SECRET_STRING);
    }
}
