<?php

namespace EbayVendor\PhpParser\Node\Scalar\MagicConst;

use EbayVendor\PhpParser\Node\Scalar\MagicConst;
class Function_ extends MagicConst
{
    public function getName()
    {
        return '__FUNCTION__';
    }
}
