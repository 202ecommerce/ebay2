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
namespace EbayVendor\PhpCsFixer\Fixer\PhpUnit;

use EbayVendor\PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use EbayVendor\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use EbayVendor\PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use EbayVendor\PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use EbayVendor\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
use EbayVendor\PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use EbayVendor\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use EbayVendor\PhpCsFixer\Tokenizer\Token;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitStrictFixer extends AbstractPhpUnitFixer implements ConfigurationDefinitionFixerInterface
{
    private static $assertionMap = ['assertAttributeEquals' => 'assertAttributeSame', 'assertAttributeNotEquals' => 'assertAttributeNotSame', 'assertEquals' => 'assertSame', 'assertNotEquals' => 'assertNotSame'];
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('PHPUnit methods like `assertSame` should be used instead of `assertEquals`.', [new CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertAttributeEquals(a(), b());
        $this->assertAttributeNotEquals(a(), b());
        $this->assertEquals(a(), b());
        $this->assertNotEquals(a(), b());
    }
}
'), new CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertAttributeEquals(a(), b());
        $this->assertAttributeNotEquals(a(), b());
        $this->assertEquals(a(), b());
        $this->assertNotEquals(a(), b());
    }
}
', ['assertions' => ['assertEquals']])], null, 'Risky when any of the functions are overridden or when testing object equality.');
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
     */
    protected function applyPhpUnitClassFix(Tokens $tokens, $startIndex, $endIndex)
    {
        $argumentsAnalyzer = new ArgumentsAnalyzer();
        $functionsAnalyzer = new FunctionsAnalyzer();
        foreach ($this->configuration['assertions'] as $methodBefore) {
            $methodAfter = self::$assertionMap[$methodBefore];
            for ($index = $startIndex; $index < $endIndex; ++$index) {
                $methodIndex = $tokens->getNextTokenOfKind($index, [[\T_STRING, $methodBefore]]);
                if (null === $methodIndex) {
                    break;
                }
                if (!$functionsAnalyzer->isTheSameClassCall($tokens, $methodIndex)) {
                    continue;
                }
                $openingParenthesisIndex = $tokens->getNextMeaningfulToken($methodIndex);
                $argumentsCount = $argumentsAnalyzer->countArguments($tokens, $openingParenthesisIndex, $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openingParenthesisIndex));
                if (2 === $argumentsCount || 3 === $argumentsCount) {
                    $tokens[$methodIndex] = new Token([\T_STRING, $methodAfter]);
                }
                $index = $methodIndex;
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolverRootless('assertions', [(new FixerOptionBuilder('assertions', 'List of assertion methods to fix.'))->setAllowedTypes(['array'])->setAllowedValues([new AllowedValueSubset(\array_keys(self::$assertionMap))])->setDefault(['assertAttributeEquals', 'assertAttributeNotEquals', 'assertEquals', 'assertNotEquals'])->getOption()], $this->getName());
    }
}
