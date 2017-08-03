<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Services\JWSProvider;

/**
 * Interface for classes that are able to create and load JSON web signatures (JWS).
 */
interface JWSProviderInterface
{
    /**
     * Creates a new JWS signature from a given payload.
     *
     * @param array $payload
     *
     * @return \JWTAuthenticationBundle\Signature\CreatedJWS
     */
    public function create(array $payload);

    /**
     * Loads an existing JWS signature from a given JWT token.
     *
     * @param string $token
     *
     * @return \JWTAuthenticationBundle\Signature\LoadedJWS
     */
    public function load($token);
}
