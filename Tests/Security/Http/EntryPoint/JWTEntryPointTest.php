<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Security\Http\EntryPoint;

use JubaopenTech\JWTAuthenticationBundle\Security\Http\EntryPoint\JWTEntryPoint;

/**
 * JWTEntryPointTest.
 */
class JWTEntryPointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test start method.
     */
    public function testStart()
    {
        $entryPoint = new JWTEntryPoint();
        $this->assertInstanceOf('Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface', $entryPoint);

        $response = $entryPoint->start($this->getRequest());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(401, $response->getStatusCode(), 'status code should be 401');

        $data = json_decode($response->getContent(), true);
        $this->assertEquals($data['code'], '401');
        $this->assertEquals($data['message'], 'Bad credentials');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRequest()
    {
        return $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
