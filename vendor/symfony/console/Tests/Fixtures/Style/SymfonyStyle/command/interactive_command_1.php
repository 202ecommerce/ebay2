<?php

namespace EbayVendor;

use EbayVendor\Symfony\Component\Console\Input\InputInterface;
use EbayVendor\Symfony\Component\Console\Output\OutputInterface;
use EbayVendor\Symfony\Component\Console\Style\SymfonyStyle;
//Ensure that questions have the expected outputs
return function (InputInterface $input, OutputInterface $output) {
    $output = new SymfonyStyle($input, $output);
    $stream = \fopen('php://memory', 'r+', \false);
    \fwrite($stream, "Foo\nBar\nBaz");
    \rewind($stream);
    $input->setStream($stream);
    $output->ask('What\'s your name?');
    $output->ask('How are you?');
    $output->ask('Where do you come from?');
};
