<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
class Foo4Command extends Command
{
    protected function configure()
    {
        $this->setName('foo3:bar:toh');
    }
}
\class_alias('EbayVendor\\Foo4Command', 'Foo4Command', \false);
