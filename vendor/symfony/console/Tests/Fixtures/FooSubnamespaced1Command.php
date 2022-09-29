<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class FooSubnamespaced1Command extends Command
{
    public $input;
    public $output;
    protected function configure()
    {
        $this->setName('foo:bar:baz')->setDescription('The foo:bar:baz command')->setAliases(['foobarbaz']);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }
}
\class_alias('EbayVendor\\FooSubnamespaced1Command', 'FooSubnamespaced1Command', \false);
