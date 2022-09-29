<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
class FooSameCaseUppercaseCommand extends Command
{
    protected function configure()
    {
        $this->setName('foo:BAR')->setDescription('foo:BAR command');
    }
}
\class_alias('EbayVendor\\FooSameCaseUppercaseCommand', 'FooSameCaseUppercaseCommand', \false);
