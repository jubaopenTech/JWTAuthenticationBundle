<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Security\User;

use JubaopenTech\JWTAuthenticationBundle\Security\User\JWTUser;
use JubaopenTech\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use JubaopenTech\JWTAuthenticationBundle\Security\User\JWTUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * JWTProviderTest.
 */
class JWTUserProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportsClass()
    {
        $userProvider = new JWTUserProvider(JWTUser::class);

        $this->assertTrue($userProvider->supportsClass(JWTUserInterface::class));
        $this->assertTrue($userProvider->supportsClass(JWTUser::class));
        $this->assertFalse($userProvider->supportsClass(UserInterface::class));
    }

    public function testLoadUserByUsername()
    {
        $userProvider = new JWTUserProvider(JWTUser::class);
        $user         = $userProvider->loadUserByUsername('jbp');

        $this->assertInstanceOf(JWTUser::class, $user);
        $this->assertSame('jbp', $user->getUsername());

        $this->assertSame($userProvider->loadUserByUsername('jbp'), $user, 'User instances should be cached.');
    }

    public function testRefreshUser()
    {
        $user = new JWTUser('jbp');
        $this->assertSame($user, (new JWTUserProvider(JWTUser::class))->refreshUser($user));
    }
}
