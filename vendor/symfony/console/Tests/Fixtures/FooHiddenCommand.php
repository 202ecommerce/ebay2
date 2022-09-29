<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class FooHiddenCommand extends Command
{
    protected function configure()
    {
        $this->setName('foo:hidden')->setAliases(['afoohidden'])->setHidden(\true);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
\class_alias('EbayVendor\\FooHiddenCommand', 'FooHiddenCommand', \false);
