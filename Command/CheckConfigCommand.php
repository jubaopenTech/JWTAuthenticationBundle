<?php

/**
 * @author zhaozhuobin
 */

namespace JWTAuthenticationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CheckConfigCommand.
 */
class CheckConfigCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jbp:jwt:check-config')
            ->setDescription('Check JWT configuration');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $keyLoader = $this->getContainer()->get('jbp_jwt_authentication.key_loader');

        try {
            $keyLoader->loadKey('public');
            $keyLoader->loadKey('private');
        } catch (\RuntimeException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');

            return 1;
        }

        $output->writeln('<info>The configuration seems correct.</info>');

        return 0;
    }
}
