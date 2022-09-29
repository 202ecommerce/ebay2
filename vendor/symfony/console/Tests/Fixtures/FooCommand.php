<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class FooCommand extends Command
{
    public $input;
    public $output;
    protected function configure()
    {
        $this->setName('foo:bar')->setDescription('The foo:bar command')->setAliases(['afoobar']);
    }
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('interact called');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $output->writeln('called');
    }
}
\class_alias('EbayVendor\\FooCommand', 'FooCommand', \false);
