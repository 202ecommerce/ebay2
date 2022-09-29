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
namespace EbayVendor\PhpCsFixer\Fixer\ReturnNotation;

use EbayVendor\PhpCsFixer\AbstractProxyFixer;
use EbayVendor\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use EbayVendor\PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use EbayVendor\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * @deprecated since 2.4, replaced by BlankLineBeforeStatementFixer
 *
 * @todo To be removed at 3.0
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 */
final class BlankLineBeforeReturnFixer extends AbstractProxyFixer implements DeprecatedFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('An empty line feed should precede a return statement.', [new CodeSample("<?php\nfunction A()\n{\n    echo 1;\n    return 1;\n}\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after NoUselessReturnFixer.
     */
    public function getPriority()
    {
        return parent::getPriority();
    }
    /**
     * {@inheritdoc}
     */
    public function getSuccessorsNames()
    {
        return \array_keys($this->proxyFixers);
    }
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        $fixer = new BlankLineBeforeStatementFixer();
        $fixer->configure(['statements' => ['return']]);
        return [$fixer];
    }
}
