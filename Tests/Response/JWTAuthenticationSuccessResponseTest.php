<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Response;

use JubaopenTech\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;

/**
 * Tests the JWTAuthenticationSuccessResponse.
 */
final class JWTAuthenticationSuccessResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testResponse()
    {
        $data = [
            'username' => 'foobar',
            'email'    => 'dev@jbp.fr',
        ];
        $expected = ['token' => 'jwt'] + $data;
        $response = new JWTAuthenticationSuccessResponse($expected['token'], $data);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode($expected), $response->getContent());

        return $response;
    }

    /**
     * @depends testResponse
     */
    public function testReplaceData(JWTAuthenticationSuccessResponse $response)
    {
        $replacementData = ['foo' => 'bar'];
        $response->setData($replacementData);

        // Test that the previous method call has no effect on the original body
        $this->assertEquals(json_encode($replacementData), $response->getContent());
        $this->assertAttributeSame($replacementData['foo'], 'foo', json_decode($response->getContent()));
        $this->assertFalse(isset(json_decode($response->getContent())->token));
    }
}
