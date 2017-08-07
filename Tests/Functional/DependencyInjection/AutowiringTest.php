<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Functional\DependencyInjection;

use JubaopenTech\JWTAuthenticationBundle\DependencyInjection\JbpJWTAuthenticationExtension;
use JubaopenTech\JWTAuthenticationBundle\Encoder\DefaultEncoder;
use JubaopenTech\JWTAuthenticationBundle\JbpJWTAuthenticationBundle;
use JubaopenTech\JWTAuthenticationBundle\Services\JWSProvider\DefaultJWSProvider;
use JubaopenTech\JWTAuthenticationBundle\Services\JWTManager;
use JubaopenTech\JWTAuthenticationBundle\Tests\Stubs\Autowired;
use JubaopenTech\JWTAuthenticationBundle\TokenExtractor\ChainTokenExtractor;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class AutowiringTest extends \PHPUnit_Framework_TestCase
{
    public function testAutowiring()
    {
        $container = $this->createContainerBuilder();
        $container->registerExtension(new SecurityExtension());
        $container->registerExtension(new FrameworkExtension());
        $container->registerExtension(new JbpJWTAuthenticationExtension());
        $container->registerExtension(new DoctrineExtension());

        (new YamlFileLoader($container, new FileLocator([__DIR__.'/../app/config'])))->load('config_default.yml');

        $container
            ->register('autowired', Autowired::class)
            ->setAutowired(true);

        $container->compile();

        $autowired = $container->get('autowired');

        $this->assertInstanceOf(JWTManager::class, $autowired->getJWTManager());
        $this->assertInstanceOf(DefaultEncoder::class, $autowired->getJWTEncoder());
        $this->assertInstanceOf(ChainTokenExtractor::class, $autowired->getTokenExtractor());
        $this->assertInstanceOf(DefaultJWSProvider::class, $autowired->getJWSProvider());
    }

    public function testAutowireConfiguredEncoderServiceForInterfaceTypeHint()
    {
        if (!method_exists(ContainerBuilder::class, 'fileExists')) {
            $this->markTestSkipped('Using the configured encoder for autowiring is supported using symfony 3.3+ only.');
        }

        $container = $this->createContainerBuilder();
        $container->registerExtension(new SecurityExtension());
        $container->registerExtension(new FrameworkExtension());
        $container->registerExtension(new JbpJWTAuthenticationExtension());
        $container->registerExtension(new DoctrineExtension());

        (new YamlFileLoader($container, new FileLocator([__DIR__.'/../app/config'])))->load('config_custom_encoder.yml');

        $container
            ->register('autowired', Autowired::class)
            ->setAutowired(true);

        $container->compile();

        $autowired = $container->get('autowired');

        $this->assertInstanceOf(DummyEncoder::class, $autowired->getJWTEncoder());
    }

    private static function createContainerBuilder()
    {
        return new ContainerBuilder(new ParameterBag([
            'kernel.bundles'          => ['FrameworkBundle' => FrameworkBundle::class, 'JbpJWTAuthenticationBundle' => JbpJWTAuthenticationBundle::class],
            'kernel.bundles_metadata' => [],
            'kernel.cache_dir'        => __DIR__,
            'kernel.debug'            => false,
            'kernel.environment'      => 'test',
            'kernel.name'             => 'kernel',
            'kernel.root_dir'         => __DIR__,
            'kernel.container_class'  => 'AutowiringTestContainer',
            'kernel.charset'          => 'utf8',
        ]));
    }
}

final class DummyEncoder extends DefaultEncoder
{
    public function __construct()
    {
    }
}
