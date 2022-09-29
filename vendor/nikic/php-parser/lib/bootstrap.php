<?php

namespace EbayVendor;

if (!\class_exists('EbayVendor\\PhpParser\\Autoloader')) {
    require __DIR__ . '/PhpParser/Autoloader.php';
}
PhpParser\Autoloader::register();
