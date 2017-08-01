<?php

/**
 * @author zhaozhuobin
 */

namespace JWTAuthenticationBundle\Exception;

/**
 * JWTDecodeFailureException is thrown if an error occurs in the token decoding process.
 */
class JWTDecodeFailureException extends JWTFailureException
{
    const INVALID_TOKEN    = 'invalid_token';
    const UNVERIFIED_TOKEN = 'unverified_token';
    const EXPIRED_TOKEN    = 'expired_token';
}
