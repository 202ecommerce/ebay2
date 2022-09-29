<?php

namespace EbayVendor\PhpParser\Node\Scalar\MagicConst;

use EbayVendor\PhpParser\Node\Scalar\MagicConst;
class File extends MagicConst
{
    public function getName()
    {
        return '__FILE__';
    }
}
