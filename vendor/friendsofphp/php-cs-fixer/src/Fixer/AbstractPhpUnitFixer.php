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
namespace EbayVendor\PhpCsFixer\Fixer;

use EbayVendor\PhpCsFixer\AbstractFixer;
use EbayVendor\PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
/**
 * @internal
 */
abstract class AbstractPhpUnitFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public final function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_CLASS, \T_STRING]);
    }
    protected final function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $phpUnitTestCaseIndicator = new PhpUnitTestCaseIndicator();
        foreach ($phpUnitTestCaseIndicator->findPhpUnitClasses($tokens) as $indices) {
            $this->applyPhpUnitClassFix($tokens, $indices[0], $indices[1]);
        }
    }
    /**
     * @param int $startIndex
     * @param int $endIndex
     */
    protected abstract function applyPhpUnitClassFix(Tokens $tokens, $startIndex, $endIndex);
    /**
     * @param int $index
     *
     * @return int
     */
    protected final function getDocBlockIndex(Tokens $tokens, $index)
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);
        } while ($tokens[$index]->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_FINAL, \T_ABSTRACT, \T_COMMENT]));
        return $index;
    }
    /**
     * @param int $index
     *
     * @return bool
     */
    protected final function isPHPDoc(Tokens $tokens, $index)
    {
        return $tokens[$index]->isGivenKind(\T_DOC_COMMENT);
    }
}
