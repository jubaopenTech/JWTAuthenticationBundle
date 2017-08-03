<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Functional\Command;

use JubaopenTech\JWTAuthenticationBundle\Command\CheckConfigCommand;
use JubaopenTech\JWTAuthenticationBundle\Tests\Functional\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * CheckOpenSSLCommandTest.
 */
class CheckConfigCommandTest extends TestCase
{
    /**
     * Test command.
     */
    public function testCheckOpenSSLCommand()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $command = new CheckConfigCommand();
        $command->setContainer($kernel->getContainer());

        $tester = new CommandTester($command);
        $this->assertEquals(0, $tester->execute([]));
        $this->assertContains('The configuration seems correct.', $tester->getDisplay());
    }
}
