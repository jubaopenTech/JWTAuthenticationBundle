<?php

namespace JWTAuthenticationBundle\Tests\Encoder;

use JWTAuthenticationBundle\Encoder\DefaultEncoder;
use JWTAuthenticationBundle\Services\JWSProvider\DefaultJWSProvider;
use JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use JWTAuthenticationBundle\Signature\CreatedJWS;
use JWTAuthenticationBundle\Signature\LoadedJWS;

/**
 * Tests the DefaultEncoder.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class DefaultEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests calling DefaultEncoder::decode() with a valid signature and payload.
     */
    public function testDecodeFromValidJWS()
    {
        $payload = [
            'username' => 'chalasr',
            'exp'      => time() + 3600,
        ];

        $loadedJWS   = new LoadedJWS($payload, 100,true);
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('load')
            ->willReturn($loadedJWS);

        $encoder = new DefaultEncoder($jwsProvider);

        $this->assertSame($payload, $encoder->decode('jwt'));
    }

    /**
     * Tests calling DefaultEncoder::encode() with a signed token.
     */
    public function testEncodeFromValidJWS()
    {
        $createdJWS  = new CreatedJWS('jwt', true);
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn($createdJWS);

        $encoder = new DefaultEncoder($jwsProvider);

        $this->assertSame('jwt', $encoder->encode([]));
    }

    /**
     * Tests that calling DefaultEncoder::encode() with an unsigned JWS correctly fails.
     *
     * @expectedException \JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function testEncodeFromUnsignedJWS()
    {
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn(new CreatedJWS('jwt', false));

        $encoder = new DefaultEncoder($jwsProvider);
        $encoder->encode([]);
    }

    /**
     * Tests that calling DefaultEncoder::decode() with an unverified signature correctly fails.
     *
     * @expectedException \JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     */
    public function testDecodeFromUnverifiedJWS()
    {
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('load')
            ->willReturn(new LoadedJWS([],100, false));

        $encoder = new DefaultEncoder($jwsProvider);
        $encoder->decode('secrettoken');
    }

    /**
     * Tests that calling DefaultEncoder::decode() with an expired payload correctly fails.
     *
     * @expectedException        \JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     * @expectedExceptionMessage Expired JWT Token
     */
    public function testDecodeFromExpiredPayload()
    {
        $loadedJWS   = new LoadedJWS(['exp' => time() - 3600],100, true);
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('load')
            ->willReturn($loadedJWS);

        $encoder = new DefaultEncoder($jwsProvider);
        $encoder->decode('jwt');
    }

    /**
     * Tests that calling DefaultEncoder::decode() with an iat set in the future correctly fails.
     *
     * @expectedException        \JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     * @expectedExceptionMessage Invalid JWT Token
     */
    public function testDecodeWithInvalidIssudAtClaimInPayload()
    {
        $loadedJWS   = new LoadedJWS(['exp' => time() + 3600, 'iat' => time() + 3600],100, true);
        $jwsProvider = $this->getJWSProviderMock();
        $jwsProvider
            ->expects($this->once())
            ->method('load')
            ->willReturn($loadedJWS);

        $encoder = new DefaultEncoder($jwsProvider);
        $encoder->decode('jwt');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getJWSProviderMock()
    {
        return $this->getMockBuilder(DefaultJWSProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
