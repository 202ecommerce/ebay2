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
namespace EbayVendor\PhpCsFixer\Fixer\Whitespace;

use EbayVendor\PhpCsFixer\AbstractFixer;
use EbayVendor\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use EbayVendor\PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use EbayVendor\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use EbayVendor\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
use EbayVendor\PhpCsFixer\Tokenizer\CT;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class NoSpacesAroundOffsetFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('There MUST NOT be spaces around offset braces.', [new CodeSample("<?php\n\$sample = \$b [ 'a' ] [ 'b' ];\n"), new CodeSample("<?php\n\$sample = \$b [ 'a' ] [ 'b' ];\n", ['positions' => ['inside']]), new CodeSample("<?php\n\$sample = \$b [ 'a' ] [ 'b' ];\n", ['positions' => ['outside']])]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(['[', CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equalsAny(['[', [CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN]])) {
                continue;
            }
            if (\in_array('inside', $this->configuration['positions'], \true)) {
                if ($token->equals('[')) {
                    $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);
                } else {
                    $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE, $index);
                }
                // remove space after opening `[` or `{`
                if ($tokens[$index + 1]->isWhitespace(" \t")) {
                    $tokens->clearAt($index + 1);
                }
                // remove space before closing `]` or `}`
                if ($tokens[$endIndex - 1]->isWhitespace(" \t")) {
                    $tokens->clearAt($endIndex - 1);
                }
            }
            if (\in_array('outside', $this->configuration['positions'], \true)) {
                $prevNonWhitespaceIndex = $tokens->getPrevNonWhitespace($index);
                if ($tokens[$prevNonWhitespaceIndex]->isComment()) {
                    continue;
                }
                $tokens->removeLeadingWhitespace($index);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $values = ['inside', 'outside'];
        return new FixerConfigurationResolverRootless('positions', [(new FixerOptionBuilder('positions', 'Whether spacing should be fixed inside and/or outside the offset braces.'))->setAllowedTypes(['array'])->setAllowedValues([new AllowedValueSubset($values)])->setDefault($values)->getOption()], $this->getName());
    }
}
