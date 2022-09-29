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
namespace EbayVendor\PhpCsFixer\Fixer\Import;

use EbayVendor\PhpCsFixer\AbstractFixer;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
use EbayVendor\PhpCsFixer\FixerDefinition\VersionSpecification;
use EbayVendor\PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use EbayVendor\PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use EbayVendor\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use EbayVendor\PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use EbayVendor\PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use EbayVendor\PhpCsFixer\Tokenizer\CT;
use EbayVendor\PhpCsFixer\Tokenizer\Generator\NamespacedStringTokenGenerator;
use EbayVendor\PhpCsFixer\Tokenizer\Resolver\TypeShortNameResolver;
use EbayVendor\PhpCsFixer\Tokenizer\Token;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author VeeWee <toonverwerft@gmail.com>
 */
final class FullyQualifiedStrictTypesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('Transforms imported FQCN parameters and return types in function arguments to short version.', [new CodeSample('<?php

use Foo\\Bar;

class SomeClass
{
    public function doSomething(\\Foo\\Bar $foo)
    {
    }
}
'), new VersionSpecificCodeSample('<?php

use Foo\\Bar;
use Foo\\Bar\\Baz;

class SomeClass
{
    public function doSomething(\\Foo\\Bar $foo): \\Foo\\Bar\\Baz
    {
    }
}
', new VersionSpecification(70000))]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoSuperfluousPhpdocTagsFixer.
     * Must run after PhpdocToReturnTypeFixer.
     */
    public function getPriority()
    {
        return 7;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_FUNCTION) && (\count((new NamespacesAnalyzer())->getDeclarations($tokens)) || \count((new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens)));
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $lastIndex = $tokens->count() - 1;
        for ($index = $lastIndex; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            // Return types are only available since PHP 7.0
            $this->fixFunctionReturnType($tokens, $index);
            $this->fixFunctionArguments($tokens, $index);
        }
    }
    /**
     * @param int $index
     */
    private function fixFunctionArguments(Tokens $tokens, $index)
    {
        $arguments = (new FunctionsAnalyzer())->getFunctionArguments($tokens, $index);
        foreach ($arguments as $argument) {
            if (!$argument->hasTypeAnalysis()) {
                continue;
            }
            $this->detectAndReplaceTypeWithShortType($tokens, $argument->getTypeAnalysis());
        }
    }
    /**
     * @param int $index
     */
    private function fixFunctionReturnType(Tokens $tokens, $index)
    {
        if (\PHP_VERSION_ID < 70000) {
            return;
        }
        $returnType = (new FunctionsAnalyzer())->getFunctionReturnType($tokens, $index);
        if (!$returnType) {
            return;
        }
        $this->detectAndReplaceTypeWithShortType($tokens, $returnType);
    }
    private function detectAndReplaceTypeWithShortType(Tokens $tokens, TypeAnalysis $type)
    {
        if ($type->isReservedType()) {
            return;
        }
        $typeName = $type->getName();
        if (0 !== \strpos($typeName, '\\')) {
            return;
        }
        $shortType = (new TypeShortNameResolver())->resolve($tokens, $typeName);
        if ($shortType === $typeName) {
            return;
        }
        $shortType = (new NamespacedStringTokenGenerator())->generate($shortType);
        if (\true === $type->isNullable()) {
            \array_unshift($shortType, new Token([CT::T_NULLABLE_TYPE, '?']));
        }
        $tokens->overrideRange($type->getStartIndex(), $type->getEndIndex(), $shortType);
    }
}
