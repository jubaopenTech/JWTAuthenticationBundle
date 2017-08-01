<?php
/**
 * @author:zhaozhuobin
 */
namespace JWTAuthenticationBundle\Providers;

use Doctrine\Bundle\DoctrineBundle\Registry;
use JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * JWT User provider.
 */
final class JWTUserProvider implements UserProviderInterface
{
    private $doctrine;

    private $encodeFactory;

    private $field;

    private $providerClass;

    /**
     * JWTUserProvider constructor.
     * @param Registry $doctrine
     * @param EncoderFactory $encodeFactory
     * @param $providerClass
     * @param $field
     */
    public function __construct(Registry $doctrine,EncoderFactory $encodeFactory,$providerClass,$field)
    {
        $this->doctrine = $doctrine;
        $this->encodeFactory = $encodeFactory;
        $this->providerClass = $providerClass;
        $this->field = $field;
    }

    /**
     * @param string $username
     * @return JWTUserInterface
     */
    public function loadUserByUsername($username)
    {
        $user = $this->doctrine->getRepository($this->providerClass)->findOneBy([
            $this->field => $username
        ]);
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        if($this instanceof UserProviderInterface){
            return true;
        }
        return false;
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }
}
