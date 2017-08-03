<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Functional;

use JubaopenTech\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use JubaopenTech\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use JubaopenTech\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use JubaopenTech\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use JubaopenTech\JWTAuthenticationBundle\Events;
use JubaopenTech\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

/**
 * Tests the overriding authentication response mechanism.
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

        $this->assertSame('Custom JWT not found message', $response['msg']);
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

        $this->assertSame('Custom JWT invalid message', $response['msg']);
    }

    public function testAccessSecuredRouteWithInvalidJWTDecodedEvent()
    {
        self::$subscriber->setListener(Events::JWT_DECODED, function (JWTDecodedEvent $e) {
            $e->markAsInvalid();
        });

        static::$client = static::createAuthenticatedClient();
        static::accessSecuredRoute();

        $responseBody = json_decode(static::$client->getResponse()->getContent(), true);

        $this->assertSame('Invalid JWT Token', $responseBody['msg']);

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

        $this->assertSame('Custom JWT Expired Token message', $response['msg']);

        self::$subscriber->unsetListener(Events::JWT_EXPIRED);
    }
}
