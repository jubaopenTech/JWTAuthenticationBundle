<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Security\Firewall;

use JubaopenTech\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use JubaopenTech\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use JubaopenTech\JWTAuthenticationBundle\Events;
use JubaopenTech\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use JubaopenTech\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use JubaopenTech\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use JubaopenTech\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * JWTListener.
 *
 * @deprecated since 2.0, will be removed in 3.0. See
 *             {@link JWTTokenAuthenticator} instead
 */
class JWTListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $tokenExtractors;

    /**
     * @param TokenStorageInterface          $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     * @param array                          $config
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        array $config = []
    ) {
        @trigger_error(sprintf('The "%s" class is deprecated since version 2.0 and will be removed in 3.0. See "%s" instead.', __CLASS__, JWTTokenAuthenticator::class), E_USER_DEPRECATED);

        $this->tokenStorage          = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->config                = array_merge(['throw_exceptions' => false], $config);
        $this->tokenExtractors       = [];
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $requestToken = $this->getRequestToken($event->getRequest());

        if (null === $requestToken) {
            $jwtNotFoundEvent = new JWTNotFoundEvent();
            $this->dispatcher->dispatch(Events::JWT_NOT_FOUND, $jwtNotFoundEvent);

            if ($response = $jwtNotFoundEvent->getResponse()) {
                $event->setResponse($response);
            }

            return;
        }

        try {
            $token = new JWTUserToken();
            $token->setRawToken($requestToken);

            $authToken = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($authToken);

            return;
        } catch (AuthenticationException $failed) {
            if ($this->config['throw_exceptions']) {
                throw $failed;
            }

            $response = new JWTAuthenticationFailureResponse($failed->getMessage());

            $jwtInvalidEvent = new JWTInvalidEvent($failed, $response);
            $this->dispatcher->dispatch(Events::JWT_INVALID, $jwtInvalidEvent);

            $event->setResponse($jwtInvalidEvent->getResponse());
        }
    }

    /**
     * @param TokenExtractorInterface $extractor
     */
    public function addTokenExtractor(TokenExtractorInterface $extractor)
    {
        $this->tokenExtractors[] = $extractor;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function getRequestToken(Request $request)
    {
        /** @var TokenExtractorInterface $tokenExtractor */
        foreach ($this->tokenExtractors as $tokenExtractor) {
            if (($token = $tokenExtractor->extract($request))) {
                return $token;
            }
        }
    }
}
