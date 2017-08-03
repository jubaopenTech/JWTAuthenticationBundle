<?php

namespace Services;

use JubaopenTech\JWTAuthenticationBundle\Events;
use JubaopenTech\JWTAuthenticationBundle\Services\JWTManager;
use JubaopenTech\JWTAuthenticationBundle\Tests\Stubs\User as CustomUser;
use Symfony\Component\Security\Core\User\User;

/**
 * JWTManagerTest.
 */
class JWTManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test create.
     */
    public function testCreate()
    {
        $dispatcher = $this->getEventDispatcherMock();
        $dispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->equalTo(Events::JWT_CREATED),
                $this->isInstanceOf('JWTAuthenticationBundle\Event\JWTCreatedEvent')
            );

        $dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->equalTo(Events::JWT_ENCODED),
                $this->isInstanceOf('JWTAuthenticationBundle\Event\JWTEncodedEvent')
            );

        $encoder = $this->getJWTEncoderMock();
        $encoder
            ->expects($this->once())
            ->method('encode')
            ->willReturn('secrettoken');

        $manager = new JWTManager($encoder, $dispatcher);
        $this->assertEquals('secrettoken', $manager->create(new User('user', 'password')));
    }

    /**
     * test decode.
     */
    public function testDecode()
    {
        $dispatcher = $this->getEventDispatcherMock();
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->equalTo(Events::JWT_DECODED),
                $this->isInstanceOf('JWTAuthenticationBundle\Event\JWTDecodedEvent')
            );

        $encoder = $this->getJWTEncoderMock();
        $encoder
            ->expects($this->once())
            ->method('decode')
            ->willReturn(['foo' => 'bar']);

        $manager = new JWTManager($encoder, $dispatcher);
        $this->assertEquals(['foo' => 'bar'], $manager->decode($this->getJWTUserTokenMock()));
    }

    /**
     * test identity field.
     */
    public function testIdentityField()
    {
        $dispatcher = $this->getEventDispatcherMock();
        $dispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->equalTo(Events::JWT_CREATED),
                $this->isInstanceOf('JWTAuthenticationBundle\Event\JWTCreatedEvent')
            );

        $dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->equalTo(Events::JWT_ENCODED),
                $this->isInstanceOf('JWTAuthenticationBundle\Event\JWTEncodedEvent')
            );

        $encoder = $this->getJWTEncoderMock();
        $encoder
            ->expects($this->once())
            ->method('encode')
            ->willReturn('secrettoken');

        $manager = new JWTManager($encoder, $dispatcher);
        $manager->setUserIdentityField('email');
        $this->assertEquals('secrettoken', $manager->create(new CustomUser('user', 'password', 'victuxbb@gmail.com')));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getJWTUserTokenMock()
    {
        $mock = $this
            ->getMockBuilder('JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken')
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('getCredentials')
            ->willReturn('secrettoken');

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getJWTEncoderMock()
    {
        return $this
            ->getMockBuilder('JWTAuthenticationBundle\Encoder\JWTEncoderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventDispatcherMock()
    {
        return $this
            ->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
