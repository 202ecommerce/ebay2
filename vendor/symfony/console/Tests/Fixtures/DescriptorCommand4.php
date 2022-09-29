<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EbayVendor\Symfony\Component\Console\Tests\Fixtures;

use EbayVendor\Symfony\Component\Console\Command\Command;
class DescriptorCommand4 extends Command
{
    protected function configure()
    {
        $this->setName('descriptor:command4')->setAliases(['descriptor:alias_command4', 'command4:descriptor']);
    }
}
