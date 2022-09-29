<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('namespace:name')->setAliases(['name'])->setDescription('description')->setHelp('help');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('execute called');
    }
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('interact called');
    }
}
\class_alias('EbayVendor\\TestCommand', 'TestCommand', \false);
