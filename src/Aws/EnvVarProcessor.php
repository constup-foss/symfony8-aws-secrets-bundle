<?php

declare(strict_types = 1);

namespace ConstupFoss\Symfony8AwsSecretsBundle\Aws;

use Closure;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class EnvVarProcessor implements EnvVarProcessorInterface
{
    private string $delimiter;
    private array $decodedSecrets = [];
    private bool $ignore;
    private EnvVarProvider $provider;

    /**
     * @param EnvVarProvider $provider
     * @param bool           $ignore
     * @param string         $delimiter
     */
    public function __construct(
        EnvVarProvider $provider,
        bool $ignore = false,
        string $delimiter = ','
    ) {
        $this->ignore = $ignore;
        $this->delimiter = $delimiter;
        $this->provider = $provider;
    }

    /**
     * Returns the value of the given variable as managed by the current instance.
     *
     * @param string  $prefix The namespace of the variable
     * @param string  $name   The name of the variable within the namespace
     * @param Closure $getEnv A closure that allows fetching more env vars
     *
     * @throws RuntimeException on error
     *
     * @return mixed
     */
    public function getEnv(string $prefix, string $name, Closure $getEnv): mixed
    {
        if ($this->ignore === true) {
            return $getEnv($name);
        }

        $value = $getEnv($name);

        $parts = explode($this->delimiter, $value);
        $result = $this->provider->get($parts[0]);

        if (isset($parts[1])) {
            if (!isset($this->decodedSecrets[$parts[0]])) {
                $this->decodedSecrets[$parts[0]] = json_decode($result, true);
            }

            return (string)$this->decodedSecrets[$parts[0]][$parts[1]];
        }

        return $result;
    }

    /**
     * @param bool $ignore
     */
    public function setIgnore(bool $ignore): void
    {
        $this->ignore = $ignore;
    }

    /**
     * @return string[] The PHP-types managed by getEnv(), keyed by prefixes
     *
     * @codeCoverageIgnore
     */
    public static function getProvidedTypes(): array
    {
        return [
            'symfony8_aws_secrets' => 'string',
        ];
    }
}
