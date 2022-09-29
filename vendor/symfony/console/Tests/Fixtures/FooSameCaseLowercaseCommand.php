<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
class FooSameCaseLowercaseCommand extends Command
{
    protected function configure()
    {
        $this->setName('foo:bar')->setDescription('foo:bar command');
    }
}
\class_alias('EbayVendor\\FooSameCaseLowercaseCommand', 'FooSameCaseLowercaseCommand', \false);
