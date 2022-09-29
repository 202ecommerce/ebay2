<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class FooSubnamespaced2Command extends Command
{
    public $input;
    public $output;
    protected function configure()
    {
        $this->setName('foo:go:bret')->setDescription('The foo:bar:go command')->setAliases(['foobargo']);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }
}
\class_alias('EbayVendor\\FooSubnamespaced2Command', 'FooSubnamespaced2Command', \false);
