<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Services\KeyLoader;

use JubaopenTech\JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;

/**
 * RawKeyLoaderTest.
 */
class RawKeyLoaderTest extends AbstractTestKeyLoader
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->keyLoader = new RawKeyLoader('private.pem', 'public.pem', 'foobar');

        parent::setup();
    }
}
