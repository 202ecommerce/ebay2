<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EbayVendor\PhpCsFixer\RuleSet\Sets;

use EbayVendor\PhpCsFixer\RuleSet\AbstractRuleSetDescription;
/**
 * @internal
 */
final class PHP80MigrationSet extends AbstractRuleSetDescription
{
    public function getRules()
    {
        return ['@PHP74Migration' => \true, 'clean_namespace' => \true, 'no_unset_cast' => \true];
    }
    public function getDescription()
    {
        return 'Rules to improve code for PHP 8.0 compatibility.';
    }
}
