<?php

namespace JWTAuthenticationBundle\Tests\Functional;

use JWTAuthenticationBundle\Event\JWTDecodedEvent;
use JWTAuthenticationBundle\Event\JWTExpiredEvent;
use JWTAuthenticationBundle\Event\JWTInvalidEvent;
use JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use JWTAuthenticationBundle\Events;
use JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

/**
 * Tests the overriding authentication response mechanism.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class SubscribedTokenAuthenticationTest extends CompleteTokenAuthenticationTest
{
    private static $subscriber;

    public static function setupBeforeClass()
    {
        parent::setupBeforeClass();

        self::$subscriber = static::$kernel->getContainer()->get('jbp_jwt_authentication.test.jwt_event_subscriber');
    }

    public function testAccessSecuredRouteWithoutToken()
    {
        self::$subscriber->setListener(Events::JWT_NOT_FOUND, function (JWTNotFoundEvent $e) {
            $response = $e->getResponse();

            if ($response instanceof JWTAuthenticationFailureResponse) {
                $response->setMessage('Custom JWT not found message');
            }
        });

        $response = parent::testAccessSecuredRouteWithoutToken();

        $this->assertSame('Custom JWT not found message', $response['message']);
    }

    public function testAccessSecuredRouteWithInvalidToken($token = 'dummy')
    {
        self::$subscriber->setListener(Events::JWT_INVALID, function (JWTInvalidEvent $e) {
            $response = $e->getResponse();

            if ($response instanceof JWTAuthenticationFailureResponse) {
                $response->setMessage('Custom JWT invalid message');
            }
        });

        $response = parent::testAccessSecuredRouteWithInvalidToken($token);

        self::$subscriber->unsetListener(Events::JWT_INVALID);

        $this->assertSame('Custom JWT invalid message', $response['message']);
    }

    public function testAccessSecuredRouteWithInvalidJWTDecodedEvent()
    {
        self::$subscriber->setListener(Events::JWT_DECODED, function (JWTDecodedEvent $e) {
            $e->markAsInvalid();
        });

        static::$client = static::createAuthenticatedClient();
        static::accessSecuredRoute();

        $responseBody = json_decode(static::$client->getResponse()->getContent(), true);

        $this->assertSame('Invalid JWT Token', $responseBody['message']);

        self::$subscriber->unsetListener(Events::JWT_DECODED);
    }

    /**
     * @group time-sensitive
     */
    public function testAccessSecuredRouteWithExpiredToken($fail = true)
    {
        self::$subscriber->setListener(Events::JWT_EXPIRED, function (JWTExpiredEvent $e) {
            $response = $e->getResponse();

            if ($response instanceof JWTAuthenticationFailureResponse) {
                $response->setMessage('Custom JWT Expired Token message');
            }
        });

        $response = parent::testAccessSecuredRouteWithExpiredToken();

        $this->assertSame('Custom JWT Expired Token message', $response['message']);

        self::$subscriber->unsetListener(Events::JWT_EXPIRED);
    }
}
