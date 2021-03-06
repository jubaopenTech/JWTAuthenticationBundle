<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * JWTTokenManagerInterface must be implemented by classes able to create/decode
 * JWT tokens.
 */
interface JWTTokenManagerInterface
{
    /**
     * @param UserInterface $user
     *
     * @return string The JWT token
     */
    public function create(UserInterface $user);

    /**
     * @param TokenInterface $token
     *
     * @return array|false The JWT token payload or false if an error occurs
     */
    public function decode(TokenInterface $token);

    /**
     * Sets the field used as identifier to load an user from a JWT payload.
     *
     * @param string
     */
    public function setUserIdentityField($field);

    /**
     * Returns the field used as identifier to load an user from a JWT payload.
     *
     * @return string
     */
    public function getUserIdentityField();
}
