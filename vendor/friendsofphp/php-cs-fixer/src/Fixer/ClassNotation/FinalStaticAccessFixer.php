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
namespace EbayVendor\PhpCsFixer\Fixer\ClassNotation;

use EbayVendor\PhpCsFixer\AbstractProxyFixer;
use EbayVendor\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * @author ntzm
 *
 * @deprecated in 2.17
 */
final class FinalStaticAccessFixer extends AbstractProxyFixer implements DeprecatedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('Converts `static` access to `self` access in `final` classes.', [new CodeSample('<?php
final class Sample
{
    public function getFoo()
    {
        return static::class;
    }
}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after FinalInternalClassFixer, PhpUnitTestCaseStaticMethodCallsFixer.
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
        return [new SelfStaticAccessorFixer()];
    }
}
