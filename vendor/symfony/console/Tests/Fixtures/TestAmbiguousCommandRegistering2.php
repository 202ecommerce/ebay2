<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class TestAmbiguousCommandRegistering2 extends Command
{
    protected function configure()
    {
        $this->setName('test-ambiguous2')->setDescription('The test-ambiguous2 command');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('test-ambiguous2');
    }
}
\class_alias('EbayVendor\\TestAmbiguousCommandRegistering2', 'TestAmbiguousCommandRegistering2', \false);
