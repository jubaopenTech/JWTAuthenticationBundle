<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\TokenExtractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * TokenExtractorInterface.
 */
interface TokenExtractorInterface
{
    /**
     * @param Request $request
     *
     * @return string|false
     */
    public function extract(Request $request);
}
