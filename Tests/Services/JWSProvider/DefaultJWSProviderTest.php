<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Services\JWSProvider;

use JubaopenTech\JWTAuthenticationBundle\Services\JWSProvider\DefaultJWSProvider;
use JubaopenTech\JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface;

/**
 * Tests the DefaultJWSProvider.
 */
final class DefaultJWSProviderTest extends AbstractJWSProviderTest
{
    public function __construct()
    {
        self::$providerClass  = DefaultJWSProvider::class;
        self::$keyLoaderClass = KeyLoaderInterface::class;
    }
}
