<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Functional;

/**
 * Tests the built-in authentication response mechanism.
 */
class DefaultTokenAuthenticationTest extends CompleteTokenAuthenticationTest
{
    public function testAccessSecuredRouteWithoutToken()
    {
        $response = parent::testAccessSecuredRouteWithoutToken();

        $this->assertEquals('JWT Token not found', $response['msg']);
    }

    public function testAccessSecuredRouteWithInvalidToken($token = 'dummy')
    {
        $response = parent::testAccessSecuredRouteWithInvalidToken($token);

        $this->assertEquals('Invalid JWT Token', $response['msg']);
    }

    /**
     * @group time-sensitive
     */
    public function testAccessSecuredRouteWithExpiredToken($fail = true)
    {
        $response = parent::testAccessSecuredRouteWithExpiredToken();

        $this->assertSame('Expired JWT Token', $response['msg']);
    }
}
