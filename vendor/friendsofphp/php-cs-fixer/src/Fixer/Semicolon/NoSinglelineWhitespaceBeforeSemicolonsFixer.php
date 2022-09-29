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
namespace EbayVendor\PhpCsFixer\Fixer\Semicolon;

use EbayVendor\PhpCsFixer\AbstractFixer;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
use EbayVendor\PhpCsFixer\Tokenizer\Token;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class NoSinglelineWhitespaceBeforeSemicolonsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('Single-line whitespace before closing semicolon are prohibited.', [new CodeSample("<?php \$this->foo() ;\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after CombineConsecutiveIssetsFixer, FunctionToConstantFixer, NoEmptyStatementFixer, SimplifiedIfReturnFixer, SingleImportPerStatementFixer.
     */
    public function getPriority()
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(';');
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals(';') || !$tokens[$index - 1]->isWhitespace(" \t")) {
                continue;
            }
            if ($tokens[$index - 2]->equals(';')) {
                // do not remove all whitespace before the semicolon because it is also whitespace after another semicolon
                if (!$tokens[$index - 1]->equals(' ')) {
                    $tokens[$index - 1] = new Token([\T_WHITESPACE, ' ']);
                }
            } elseif (!$tokens[$index - 2]->isComment()) {
                $tokens->clearAt($index - 1);
            }
        }
    }
}
