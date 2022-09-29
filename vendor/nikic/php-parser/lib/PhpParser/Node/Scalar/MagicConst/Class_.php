<?php

namespace EbayVendor\PhpParser\Node\Scalar\MagicConst;

use EbayVendor\PhpParser\Node\Scalar\MagicConst;
class Class_ extends MagicConst
{
    public function getName()
    {
        return '__CLASS__';
    }
}
