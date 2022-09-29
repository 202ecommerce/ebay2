<?php

namespace EbayVendor\PhpParser\Node\Scalar\MagicConst;

use EbayVendor\PhpParser\Node\Scalar\MagicConst;
class Namespace_ extends MagicConst
{
    public function getName()
    {
        return '__NAMESPACE__';
    }
}
