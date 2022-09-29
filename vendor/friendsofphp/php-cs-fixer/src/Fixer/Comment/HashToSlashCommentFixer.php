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
namespace EbayVendor\PhpCsFixer\Fixer\Comment;

use EbayVendor\PhpCsFixer\AbstractProxyFixer;
use EbayVendor\PhpCsFixer\Fixer\DeprecatedFixerInterface;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
/**
 * Changes single comments prefixes '#' with '//'.
 *
 * @author SpacePossum
 *
 * @deprecated in 2.4, proxy to SingleLineCommentStyleFixer
 */
final class HashToSlashCommentFixer extends AbstractProxyFixer implements DeprecatedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('Single line comments should use double slashes `//` and not hash `#`.', [new CodeSample("<?php # comment\n")]);
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
        $fixer = new SingleLineCommentStyleFixer();
        $fixer->configure(['comment_types' => ['hash']]);
        return [$fixer];
    }
}
