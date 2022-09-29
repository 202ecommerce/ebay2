<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class Foo1Command extends Command
{
    public $input;
    public $output;
    protected function configure()
    {
        $this->setName('foo:bar1')->setDescription('The foo:bar1 command')->setAliases(['afoobar1']);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }
}
\class_alias('EbayVendor\\Foo1Command', 'Foo1Command', \false);
