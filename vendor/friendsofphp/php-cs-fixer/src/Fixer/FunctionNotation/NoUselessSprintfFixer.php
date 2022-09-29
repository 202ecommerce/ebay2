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
namespace EbayVendor\PhpCsFixer\Fixer\FunctionNotation;

use EbayVendor\PhpCsFixer\AbstractFixer;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
use EbayVendor\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use EbayVendor\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
final class NoUselessSprintfFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('There must be no `sprintf` calls with only the first argument.', [new CodeSample("<?php\n\$foo = sprintf('bar');\n")], null, 'Risky when if the `sprintf` function is overridden.');
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_STRING);
    }
    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * Must run before MethodArgumentSpaceFixer, NativeFunctionCasingFixer, NoEmptyStatementFixer, NoExtraBlankLinesFixer, NoSpacesInsideParenthesisFixer.
     */
    public function getPriority()
    {
        return 42;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $functionAnalyzer = new FunctionsAnalyzer();
        $argumentsAnalyzer = new ArgumentsAnalyzer();
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_STRING)) {
                continue;
            }
            if ('sprintf' !== \strtolower($tokens[$index]->getContent())) {
                continue;
            }
            if (!$functionAnalyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }
            $openParenthesisIndex = $tokens->getNextTokenOfKind($index, ['(']);
            if ($tokens[$tokens->getNextMeaningfulToken($openParenthesisIndex)]->isGivenKind(\T_ELLIPSIS)) {
                continue;
            }
            $closeParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesisIndex);
            if (1 !== $argumentsAnalyzer->countArguments($tokens, $openParenthesisIndex, $closeParenthesisIndex)) {
                continue;
            }
            $tokens->clearTokenAndMergeSurroundingWhitespace($closeParenthesisIndex);
            $prevMeaningfulTokenIndex = $tokens->getPrevMeaningfulToken($closeParenthesisIndex);
            if ($tokens[$prevMeaningfulTokenIndex]->equals(',')) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($prevMeaningfulTokenIndex);
            }
            $tokens->clearTokenAndMergeSurroundingWhitespace($openParenthesisIndex);
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            $prevMeaningfulTokenIndex = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$prevMeaningfulTokenIndex]->isGivenKind(\T_NS_SEPARATOR)) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($prevMeaningfulTokenIndex);
            }
        }
    }
}
