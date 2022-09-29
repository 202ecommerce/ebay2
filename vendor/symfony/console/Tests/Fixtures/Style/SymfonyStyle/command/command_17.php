<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
use EbayVendor\Symfony\Component\Console\Style\SymfonyStyle;
//Ensure symfony style helper methods handle trailing backslashes properly when decorating user texts
return function (InputInterface $input, OutputInterface $output) {
    $output = new SymfonyStyle($input, $output);
    $output->title('Title ending with \\');
    $output->section('Section ending with \\');
};
