<?php

namespace EbayVendor\PhpParser\Node\Scalar\MagicConst;

use EbayVendor\PhpParser\Node\Scalar\MagicConst;
class Method extends MagicConst
{
    public function getName()
    {
        return '__METHOD__';
    }
}
