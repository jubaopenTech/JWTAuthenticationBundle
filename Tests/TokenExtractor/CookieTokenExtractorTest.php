<?php

namespace JWTAuthenticationBundle\Tests\TokenExtractor;

use JWTAuthenticationBundle\TokenExtractor\CookieTokenExtractor;
use Symfony\Component\HttpFoundation\Request;

/**
 * CookieTokenExtractorTest.
 */
class CookieTokenExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test getRequestToken.
     */
    public function testGetTokenRequest()
    {
        $extractor = new CookieTokenExtractor('BEARER');

        $request = new Request();
        $this->assertFalse($extractor->extract($request));

        $request = new Request();
        $request->cookies->add(['BEAR' => 'testtoken']);
        $this->assertFalse($extractor->extract($request));

        $request = new Request();
        $request->cookies->add(['BEARER' => 'testtoken']);
        $this->assertEquals('testtoken', $extractor->extract($request));
    }
}
