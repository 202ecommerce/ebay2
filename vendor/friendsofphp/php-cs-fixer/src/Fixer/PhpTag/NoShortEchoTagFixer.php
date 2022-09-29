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
namespace EbayVendor\PhpCsFixer\Fixer\PhpTag;

use EbayVendor\PhpCsFixer\AbstractProxyFixer;
use EbayVendor\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * @author Vincent Klaiber <hello@vinkla.com>
 *
 * @deprecated proxy to EchoTagSyntaxFixer
 */
final class NoShortEchoTagFixer extends AbstractProxyFixer implements DeprecatedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('Replaces short-echo `<?=` with long format `<?php echo` syntax.', [new CodeSample("<?= \"foo\";\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoMixedEchoPrintFixer.
     */
    public function getPriority()
    {
        return 0;
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
        return [new EchoTagSyntaxFixer()];
    }
}
