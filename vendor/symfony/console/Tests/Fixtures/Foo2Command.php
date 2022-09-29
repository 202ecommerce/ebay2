<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class Foo2Command extends Command
{
    protected function configure()
    {
        $this->setName('foo1:bar')->setDescription('The foo1:bar command')->setAliases(['afoobar2']);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
\class_alias('EbayVendor\\Foo2Command', 'Foo2Command', \false);
