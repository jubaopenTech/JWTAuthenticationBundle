<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Encoder;

use JubaopenTech\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use JubaopenTech\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;

/**
 * JWTEncoderInterface.
 */
interface JWTEncoderInterface
{
    /**
     * @param array $data
     *
     * @return string the encoded token string
     *
     * @throws JWTEncodeFailureException If an error occurred while trying to create
     *                                   the token (invalid crypto key, invalid payload...)
     */
    public function encode(array $data);

    /**
     * @param string $token
     *
     * @return array
     *
     * @throws JWTDecodeFailureException If an error occurred while trying to load the token
     *                                   (invalid signature, invalid crypto key, expired token...)
     */
    public function decode($token);
}
