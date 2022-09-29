<?php

namespace EbayVendor\PhpParser\Node\Scalar\MagicConst;

use EbayVendor\PhpParser\Node\Scalar\MagicConst;
class Line extends MagicConst
{
    public function getName()
    {
        return '__LINE__';
    }
}
