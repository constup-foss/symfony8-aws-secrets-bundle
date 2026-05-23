<?php

declare(strict_types = 1);

namespace ConstupFoss\Symfony8AwsSecretsBundle;

use Aws\SecretsManager\SecretsManagerClient;
use ConstupFoss\Symfony8AwsSecretsBundle\Aws\AwsSecretsManagerClientFactory;
use ConstupFoss\Symfony8AwsSecretsBundle\Aws\EnvVarProcessor;
use ConstupFoss\Symfony8AwsSecretsBundle\Aws\EnvVarProvider;
use Exception;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class Symfony8AwsSecretsBundle extends AbstractBundle
{
    /**
     * @param DefinitionConfigurator $definition
     *
     * @return void
     */
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('client_config')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('region')->defaultNull()->end()
                        ->scalarNode('version')->defaultValue('latest')->end()
                        ->scalarNode('endpoint')->defaultNull()->end()
                        ->scalarNode('profile')->defaultNull()->end()
                        ->arrayNode('credentials')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('key')->defaultNull()->end()
                            ->scalarNode('secret')->defaultNull()->end()
                            ->scalarNode('token')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->scalarNode('delimiter')->defaultValue(',')->end()
            ->scalarNode('ignore')->defaultFalse()->end();
    }

    /**
     * @param array                 $config
     * @param ContainerConfigurator $configurator
     * @param ContainerBuilder      $container
     *
     * @throws Exception
     *
     * @return void
     */
    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        if (!class_exists(SecretsManagerClient::class)) {
            throw new Exception('The "aws/aws-sdk-php" package is required to use this bundle.');
        }

        $container->setParameter('symfony8_aws_secrets.ignore', $config['ignore']);
        $container->setParameter('symfony8_aws_secrets.delimiter', $config['delimiter']);

        $container->register('symfony8_aws_secrets.secrets_manager_client', SecretsManagerClient::class)
            ->setLazy(true)
            ->setPublic(false)
            ->addArgument($config['client_config']['region'])
            ->addArgument($config['client_config']['version'])
            ->addArgument($config['client_config']['endpoint'])
            ->addArgument($config['client_config']['profile'])
            ->addArgument($config['client_config']['credentials']['token'])
            ->addArgument($config['client_config']['credentials']['key'])
            ->addArgument($config['client_config']['credentials']['secret'])
            ->setFactory([AwsSecretsManagerClientFactory::class, 'createClient']);

        $container->setAlias('symfony8_aws_secrets.client', 'symfony8_aws_secrets.secrets_manager_client')
            ->setPublic(false);

        $container->register('symfony8_aws_secrets.env_var_provider', EnvVarProvider::class)
            ->setArgument('$secretsManagerClient', new Reference('symfony8_aws_secrets.client'))
            ->setPublic(false);

        $container->register('symfony8_aws_secrets.env_var_processor', EnvVarProcessor::class)
            ->setArgument('$provider', new Reference('symfony8_aws_secrets.env_var_provider'))
            ->setArgument('$ignore', $container->getParameter('symfony8_aws_secrets.ignore'))
            ->setArgument('$delimiter', $container->getParameter('symfony8_aws_secrets.delimiter'))
            ->setPublic(false)
            ->addTag('container.env_var_processor');
    }
}
