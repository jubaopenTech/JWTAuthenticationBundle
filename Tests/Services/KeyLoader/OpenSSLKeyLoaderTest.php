<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Services\KeyLoader;

use JubaopenTech\JWTAuthenticationBundle\Services\KeyLoader\OpenSSLKeyLoader;

/**
 * OpenSSLKeyLoaderTest.
 */
class OpenSSLKeyLoaderTest extends AbstractTestKeyLoader
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->keyLoader = new OpenSSLKeyLoader('private.pem', 'public.pem', 'foobar');

        parent::setup();
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Failed to load public key "public.pem":
     *  0906D06C:PEM routines:PEM_read_bio:no start line
     *  0906D06C:PEM routines:PEM_read_bio:no start line
     *  0906D06C:PEM routines:PEM_read_bio:no start line
     *  0906D06C:PEM routines:PEM_read_bio:no start line
     */
    public function testLoadInvalidPublicKey()
    {
        touch('public.pem');

        $this->keyLoader->loadKey('public');
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Failed to load private key "private.pem":
     *  0906D06C:PEM routines:PEM_read_bio:no start line
     */
    public function testLoadInvalidPrivateKey()
    {
        touch('private.pem');

        $this->keyLoader->loadKey('private');
    }
}
