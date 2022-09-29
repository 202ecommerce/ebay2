<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class TestAmbiguousCommandRegistering extends Command
{
    protected function configure()
    {
        $this->setName('test-ambiguous')->setDescription('The test-ambiguous command')->setAliases(['test']);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('test-ambiguous');
    }
}
\class_alias('EbayVendor\\TestAmbiguousCommandRegistering', 'TestAmbiguousCommandRegistering', \false);
