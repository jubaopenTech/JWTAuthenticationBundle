<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Security\Authentication\Token;

use Symfony\Component\Security\Guard\Token\PreAuthenticationGuardToken;

/**
 * PreAuthenticationJWTUserToken.
 */
final class PreAuthenticationJWTUserToken extends PreAuthenticationGuardToken
{
    /**
     * @var string
     */
    private $rawToken;

    /**
     * @var array
     */
    private $payload;

    /**
     * @param string $rawToken
     */
    public function __construct($rawToken)
    {
        $this->rawToken = $rawToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return $this->rawToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
