<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PemCommand.
 */
class PemCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pem:create')
            ->setDescription('Create Public Pem and Private Pem');
        $this->setHelp("Create Public Pem and Private Pem");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $privatePemPath = $this->getContainer()->getParameter('jwt_private_key_path');
            $publicPemPath = $this->getContainer()->getParameter('jwt_public_key_path');
            shell_exec("openssl genrsa -des3 -out $privatePemPath 2048;");
            shell_exec("openssl rsa -in $privatePemPath -pubout -out $publicPemPath;");
            if(!file_exists("$privatePemPath") || !file_exists("$publicPemPath")){
                throw new \RuntimeException('Fail');
            }
            $output->writeln('<info>Success.</info>');
            return 0;
        } catch (\RuntimeException $e) {
            $output->writeln('<error>Fail</error>');
            return 1;
        }
    }
}
