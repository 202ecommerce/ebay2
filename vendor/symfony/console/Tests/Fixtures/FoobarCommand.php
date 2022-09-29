<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class FoobarCommand extends Command
{
    public $input;
    public $output;
    protected function configure()
    {
        $this->setName('foobar:foo')->setDescription('The foobar:foo command');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }
}
\class_alias('EbayVendor\\FoobarCommand', 'FoobarCommand', \false);
