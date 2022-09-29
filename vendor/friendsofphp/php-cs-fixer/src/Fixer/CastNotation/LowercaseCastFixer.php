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
namespace EbayVendor\PhpCsFixer\Fixer\CastNotation;

use EbayVendor\PhpCsFixer\AbstractFixer;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
use EbayVendor\PhpCsFixer\FixerDefinition\VersionSpecification;
use EbayVendor\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use EbayVendor\PhpCsFixer\Tokenizer\Token;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class LowercaseCastFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('Cast should be written in lower case.', [new VersionSpecificCodeSample('<?php
    $a = (BOOLEAN) $b;
    $a = (BOOL) $b;
    $a = (INTEGER) $b;
    $a = (INT) $b;
    $a = (DOUBLE) $b;
    $a = (FLoaT) $b;
    $a = (reaL) $b;
    $a = (flOAT) $b;
    $a = (sTRING) $b;
    $a = (ARRAy) $b;
    $a = (OBJect) $b;
    $a = (UNset) $b;
    $a = (Binary) $b;
', new VersionSpecification(null, 70399)), new VersionSpecificCodeSample('<?php
    $a = (BOOLEAN) $b;
    $a = (BOOL) $b;
    $a = (INTEGER) $b;
    $a = (INT) $b;
    $a = (DOUBLE) $b;
    $a = (FLoaT) $b;
    $a = (flOAT) $b;
    $a = (sTRING) $b;
    $a = (ARRAy) $b;
    $a = (OBJect) $b;
    $a = (UNset) $b;
    $a = (Binary) $b;
', new VersionSpecification(70400))]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getCastTokenKinds());
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            if (!$tokens[$index]->isCast()) {
                continue;
            }
            $tokens[$index] = new Token([$tokens[$index]->getId(), \strtolower($tokens[$index]->getContent())]);
        }
    }
}
