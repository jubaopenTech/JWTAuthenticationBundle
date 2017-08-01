<?php

/**
 * @author zhaozhuobin
 */

namespace JWTAuthenticationBundle\Event;

/**
 * JWTExpiredEvent.
 */
class JWTExpiredEvent extends AuthenticationFailureEvent implements JWTFailureEventInterface
{
}
