<?php

/**
 * @author zhaozhuobin
 */

namespace JWTAuthenticationBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * JWTCreatedEvent.
 */
class JWTResponseEvent extends Event
{
    const JWT_EVENT_RESPONSE = 'jwt_event_response';

    public $container;
    public $data;
    public $result;

    public function __construct($container,array $data) {
        $this->container = $container;
        $this->setData($data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
}
