<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Security\Http\Authentication;

use JubaopenTech\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use JubaopenTech\JWTAuthenticationBundle\Events;
use JubaopenTech\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * AuthenticationFailureHandler.
 */
class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $event = new AuthenticationFailureEvent($exception, new JWTAuthenticationFailureResponse());

        $this->dispatcher->dispatch(Events::AUTHENTICATION_FAILURE, $event);

        return $event->getResponse();
    }
}
