<?php

namespace EbayVendor\PrestaShop\CodingStandards\Command;

use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Input\InputOption;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
use EbayVendor\Symfony\Component\Filesystem\Filesystem;
class CsFixerInitCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('cs-fixer:init')->setDescription('Initialize Cs Fixer environement')->addOption('dest', null, InputOption::VALUE_REQUIRED, 'Where the configuration will be stored', '.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $directory = __DIR__ . '/../../templates/cs-fixer/';
        $destination = $input->getOption('dest');
        foreach (['php_cs.dist'] as $template) {
            $this->copyFile($input, $output, $directory . $template, $destination . '/.' . $template);
        }
        return 0;
    }
}
