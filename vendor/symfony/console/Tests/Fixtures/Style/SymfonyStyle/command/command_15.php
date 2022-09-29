<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
use EbayVendor\Symfony\Component\Console\Style\SymfonyStyle;
// ensure that block() behaves properly with a type and without prefix
return function (InputInterface $input, OutputInterface $output) {
    $output = new SymfonyStyle($input, $output);
    $output->block('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', 'TEST');
};
