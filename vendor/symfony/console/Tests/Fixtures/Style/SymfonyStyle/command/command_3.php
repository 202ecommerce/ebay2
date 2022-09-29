<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
use EbayVendor\Symfony\Component\Console\Style\SymfonyStyle;
//Ensure has single blank line between two titles
return function (InputInterface $input, OutputInterface $output) {
    $output = new SymfonyStyle($input, $output);
    $output->title('First title');
    $output->title('Second title');
};
