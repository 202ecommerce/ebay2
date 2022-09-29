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
namespace EbayVendor\PhpCsFixer\Fixer\ListNotation;

use EbayVendor\PhpCsFixer\AbstractFixer;
use EbayVendor\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use EbayVendor\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use EbayVendor\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use EbayVendor\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
use EbayVendor\PhpCsFixer\FixerDefinition\VersionSpecification;
use EbayVendor\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use EbayVendor\PhpCsFixer\Tokenizer\CT;
use EbayVendor\PhpCsFixer\Tokenizer\Token;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class ListSyntaxFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @var null|int
     */
    private $candidateTokenKind;
    /**
     * Use 'syntax' => 'long'|'short'.
     *
     * @param null|array<string, string> $configuration
     *
     * @throws InvalidFixerConfigurationException
     */
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);
        $this->candidateTokenKind = 'long' === $this->configuration['syntax'] ? CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN : \T_LIST;
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.', [new VersionSpecificCodeSample("<?php\n[\$sample] = \$array;\n", new VersionSpecification(70100)), new VersionSpecificCodeSample("<?php\nlist(\$sample) = \$array;\n", new VersionSpecification(70100), ['syntax' => 'short'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer, TernaryOperatorSpacesFixer.
     */
    public function getPriority()
    {
        return 1;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return \PHP_VERSION_ID >= 70100 && $tokens->isTokenKindFound($this->candidateTokenKind);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if ($tokens[$index]->isGivenKind($this->candidateTokenKind)) {
                if (\T_LIST === $this->candidateTokenKind) {
                    $this->fixToShortSyntax($tokens, $index);
                } else {
                    $this->fixToLongSyntax($tokens, $index);
                }
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([(new FixerOptionBuilder('syntax', 'Whether to use the `long` or `short` `list` syntax.'))->setAllowedValues(['long', 'short'])->setDefault('long')->getOption()]);
    }
    /**
     * @param int $index
     */
    private function fixToLongSyntax(Tokens $tokens, $index)
    {
        static $typesOfInterest = [[CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE], '['];
        $closeIndex = $tokens->getNextTokenOfKind($index, $typesOfInterest);
        if (!$tokens[$closeIndex]->isGivenKind(CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE)) {
            return;
        }
        $tokens[$index] = new Token('(');
        $tokens[$closeIndex] = new Token(')');
        $tokens->insertAt($index, new Token([\T_LIST, 'list']));
    }
    /**
     * @param int $index
     */
    private function fixToShortSyntax(Tokens $tokens, $index)
    {
        $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
        $tokens[$openIndex] = new Token([CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN, '[']);
        $tokens[$closeIndex] = new Token([CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE, ']']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }
}
