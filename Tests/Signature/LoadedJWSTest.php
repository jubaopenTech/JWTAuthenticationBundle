<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Signature;

use JubaopenTech\JWTAuthenticationBundle\Signature\LoadedJWS;

/**
 * Tests the CreatedJWS model class.
 */
final class LoadedJWSTest extends \PHPUnit_Framework_TestCase
{
    private $goodPayload;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->goodPayload = [
            'username' => 'chalasr',
            'exp'      => time() + 3600,
            'iat'      => time(),
        ];
    }

    public function testVerifiedWithEmptyPayload()
    {
        $jws = new LoadedJWS($payload = [],100, true);

        $this->assertSame($payload, $jws->getPayload());
        $this->assertFalse($jws->isVerified());
        $this->assertFalse($jws->isExpired());
    }

    public function testUnverifiedWithGoodPayload()
    {
        $jws = new LoadedJWS($this->goodPayload,100, false);

        $this->assertSame($this->goodPayload, $jws->getPayload());
        $this->assertFalse($jws->isExpired());
        $this->assertFalse($jws->isVerified());
    }

    public function testVerifiedWithGoodPayload()
    {
        $jws = new LoadedJWS($this->goodPayload,100, true);

        $this->assertSame($this->goodPayload, $jws->getPayload());
        $this->assertFalse($jws->isExpired());
        $this->assertTrue($jws->isVerified());
    }

    public function testVerifiedWithExpiredPayload()
    {
        $payload = $this->goodPayload;
        $payload['exp'] -= 3600;

        $jws = new LoadedJWS($payload,100, true);

        $this->assertFalse($jws->isVerified());
        $this->assertTrue($jws->isExpired());
    }

    public function testIsInvalidReturnsTrueWithIssuedAtSetInTheFuture()
    {
        $payload = $this->goodPayload;
        $payload['iat'] += 3600;

        $jws = new LoadedJWS($payload,100, true);

        $this->assertFalse($jws->isVerified());
        $this->assertFalse($jws->isExpired());
        $this->assertTrue($jws->isInvalid());
    }
}
