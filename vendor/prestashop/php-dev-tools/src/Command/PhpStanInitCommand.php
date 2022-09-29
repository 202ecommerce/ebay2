<?php

namespace EbayVendor\PrestaShop\CodingStandards\Command;

use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Input\InputOption;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
use EbayVendor\Symfony\Component\Filesystem\Filesystem;
class PhpStanInitCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('phpstan:init')->setDescription('Initialize phpstan environement')->addOption('dest', null, InputOption::VALUE_REQUIRED, 'Where the configuration will be stored', 'tests/phpstan');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $directory = __DIR__ . '/../../templates/phpstan/';
        $destination = $input->getOption('dest');
        foreach (['phpstan.neon'] as $template) {
            $this->copyFile($input, $output, $directory . $template, $destination . '/' . $template);
        }
        return 0;
    }
}
