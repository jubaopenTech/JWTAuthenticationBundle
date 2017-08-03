<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Services\JWSProvider;

use JubaopenTech\JWTAuthenticationBundle\Services\JWSProvider\LcobucciJWSProvider;
use JubaopenTech\JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;

/**
 * Tests the LcobucciJWSProvider.
 */
final class LcobucciJWSProviderTest extends AbstractJWSProviderTest
{
    public function __construct()
    {
        self::$providerClass  = LcobucciJWSProvider::class;
        self::$keyLoaderClass = RawKeyLoader::class;
    }
}
