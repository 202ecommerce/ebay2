<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Command\LockableTrait;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class FooLock2Command extends Command
{
    use LockableTrait;
    protected function configure()
    {
        $this->setName('foo:lock2');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->lock();
            $this->lock();
        } catch (\LogicException $e) {
            return 1;
        }
        return 2;
    }
}
\class_alias('EbayVendor\\FooLock2Command', 'FooLock2Command', \false);
