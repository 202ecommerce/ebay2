<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Command\LockableTrait;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class FooLockCommand extends Command
{
    use LockableTrait;
    protected function configure()
    {
        $this->setName('foo:lock');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            return 1;
        }
        $this->release();
        return 2;
    }
}
\class_alias('EbayVendor\\FooLockCommand', 'FooLockCommand', \false);
