<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * AuthenticationFailureEvent.
 */
class AuthenticationFailureEvent extends Event
{
    /**
     * @var AuthenticationException
     */
    protected $exception;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param AuthenticationException $exception
     * @param Response                $response
     */
    public function __construct(AuthenticationException $exception, Response $response)
    {
        $this->exception = $exception;
        $this->response  = $response;
    }

    /**
     * @return AuthenticationException
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
