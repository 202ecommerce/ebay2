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
namespace EbayVendor\PhpCsFixer\Fixer\ControlStructure;

use EbayVendor\PhpCsFixer\AbstractFixer;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
/**
 * Fixer for rules defined in PSR2 ¶5.2.
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class SwitchCaseSpaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('Removes extra spaces between colon and case value.', [new CodeSample('<?php
    switch($a) {
        case 1   :
            break;
        default     :
            return 2;
    }
')]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_CASE, \T_DEFAULT]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind([\T_CASE, \T_DEFAULT])) {
                continue;
            }
            $ternariesCount = 0;
            for ($colonIndex = $index + 1;; ++$colonIndex) {
                // We have to skip ternary case for colons.
                if ($tokens[$colonIndex]->equals('?')) {
                    ++$ternariesCount;
                }
                if ($tokens[$colonIndex]->equalsAny([':', ';'])) {
                    if (0 === $ternariesCount) {
                        break;
                    }
                    --$ternariesCount;
                }
            }
            $valueIndex = $tokens->getPrevNonWhitespace($colonIndex);
            // skip if there is no space between the colon and previous token or is space after comment
            if ($valueIndex === $colonIndex - 1 || $tokens[$valueIndex]->isComment()) {
                continue;
            }
            $tokens->clearAt($valueIndex + 1);
        }
    }
}
