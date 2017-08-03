<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\TokenExtractor;

use JubaopenTech\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;

/**
 * AuthorizationHeaderTokenExtractorTest.
 */
class AuthorizationHeaderTokenExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test getRequestToken.
     */
    public function testGetTokenRequest()
    {
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');

        $request = new Request();
        $this->assertFalse($extractor->extract($request));

        $request = new Request();
        $request->headers->set('Authorization', 'Bear testtoken');
        $this->assertFalse($extractor->extract($request));

        $request = new Request();
        $request->headers->set('Authorizat', 'Bearer testtoken');
        $this->assertFalse($extractor->extract($request));

        $request = new Request();
        $request->headers->set('Authorization', 'Bearer testtoken');
        $this->assertEquals('testtoken', $extractor->extract($request));
    }
}
