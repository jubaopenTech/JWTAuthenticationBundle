<?php

/**
 * @author zhaozhuobin
 */

namespace JWTAuthenticationBundle\EventListener;

use JWTAuthenticationBundle\Signature\LoadedJWS;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ResponseListener fixes the Response headers based on the Request.
 */
class ResponseListener implements EventSubscriberInterface
{
    private $charset;
    private $container;

    public function __construct($charset,$container)
    {
        $this->charset = $charset;
        $this->container = $container;
    }

    /**
     * Filters the Response.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();

        if($this->container->get('jbp_jwt_authentication.encoder.default')->getState() == LoadedJWS::REFRESH){
            $payload['iat'] = time();
            $payload['exp'] = time()+$this->container->getParameter('jbp_jwt_authentication.token_ttl');
            $newToken = $this->container->get('jbp_jwt_authentication.encoder.default')->encode($payload);
            $response->headers->set(
                $this->container->getParameter('authorization_header_key'),
                $this->container->getParameter('authorization_prefix').' '.$newToken
            );
        }

        if (null === $response->getCharset()) {
            $response->setCharset($this->charset);
        }

        $response->prepare($event->getRequest());
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => 'onKernelResponse',
        );
    }
}
