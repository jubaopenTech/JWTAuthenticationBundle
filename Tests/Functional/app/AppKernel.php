<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Functional;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * AppKernel.
 */
class AppKernel extends Kernel
{
    private $encoder;
    private $userProvider;

    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        $this->encoder      = getenv('ENCODER') ?: 'default';
        $this->userProvider = getenv('PROVIDER') ?: 'in_memory';
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \JubaopenTech\JWTAuthenticationBundle\JBPJWTAuthenticationBundle(),
            new \JubaopenTech\JWTAuthenticationBundle\Tests\Functional\Bundle\Bundle(),
        ];
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir().'/JBPJWTAuthenticationBundle/cache';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return sys_get_temp_dir().'/JBPJWTAuthenticationBundle/logs';
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.sprintf('/config/config_%s.yml', $this->encoder));
        $loader->load(__DIR__.sprintf('/config/security_%s.yml', $this->userProvider));
    }

    public function getUserProvider()
    {
        return $this->userProvider;
    }

    public function getEncoder()
    {
        return $this->encoder;
    }
}
