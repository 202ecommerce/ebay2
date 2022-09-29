<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
class Foo6Command extends Command
{
    protected function configure()
    {
        $this->setName('0foo:bar')->setDescription('0foo:bar command');
    }
}
\class_alias('EbayVendor\\Foo6Command', 'Foo6Command', \false);
