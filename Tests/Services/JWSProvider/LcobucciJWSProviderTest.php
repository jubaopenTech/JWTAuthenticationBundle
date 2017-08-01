<?php

namespace JWTAuthenticationBundle\Tests\Services\JWSProvider;

use JWTAuthenticationBundle\Services\JWSProvider\LcobucciJWSProvider;
use JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;

/**
 * Tests the LcobucciJWSProvider.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class LcobucciJWSProviderTest extends AbstractJWSProviderTest
{
    public function __construct()
    {
        self::$providerClass  = LcobucciJWSProvider::class;
        self::$keyLoaderClass = RawKeyLoader::class;
    }
}
