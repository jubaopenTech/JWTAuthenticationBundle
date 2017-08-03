<?php

namespace JWTAuthenticationBundle\Tests\TokenExtractor;

use JWTAuthenticationBundle\TokenExtractor\QueryParameterTokenExtractor;
use Symfony\Component\HttpFoundation\Request;

/**
 * QueryParameterTokenExtractorTest.
 */
class QueryParameterTokenExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test getRequestToken.
     */
    public function testGetTokenRequest()
    {
        $extractor = new QueryParameterTokenExtractor('bearer');

        $request = new Request(['bear' => 'testtoken']);
        $this->assertFalse($extractor->extract($request));

        $request = new Request(['bearer' => 'testtoken']);
        $this->assertEquals('testtoken', $extractor->extract($request));
    }
}
