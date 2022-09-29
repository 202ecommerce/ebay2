<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
class BarBucCommand extends Command
{
    protected function configure()
    {
        $this->setName('bar:buc');
    }
}
\class_alias('EbayVendor\\BarBucCommand', 'BarBucCommand', \false);
