<?php

/**
 * @author zhaozhuobin
 */

namespace JWTAuthenticationBundle\Exception;

/**
 * JWTEncodeFailureException is thrown if an error occurs in the token encoding process.
 */
class JWTEncodeFailureException extends JWTFailureException
{
    const INVALID_CONFIG = 'invalid_config';
    const UNSIGNED_TOKEN = 'unsigned_token';
}
