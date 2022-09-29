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

use EbayVendor\PhpCsFixer\AbstractFixer;
use EbayVendor\PhpCsFixer\FixerDefinition\CodeSample;
use EbayVendor\PhpCsFixer\FixerDefinition\FixerDefinition;
use EbayVendor\PhpCsFixer\Preg;
use EbayVendor\PhpCsFixer\Tokenizer\Token;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class MultilineCommentOpeningClosingFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition('DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.', [new CodeSample(<<<'EOT'
<?php

namespace EbayVendor;

/******
 * Multiline comment with arbitrary asterisks count
 ******/
/**\
 * Multiline comment that seems a DocBlock
 */
/**
 * DocBlock with arbitrary asterisk count at the end
 **/

EOT
)]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_COMMENT, \T_DOC_COMMENT]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            $originalContent = $token->getContent();
            if (!$token->isGivenKind(\T_DOC_COMMENT) && !($token->isGivenKind(\T_COMMENT) && 0 === \strpos($originalContent, '/*'))) {
                continue;
            }
            $newContent = $originalContent;
            // Fix opening
            if ($token->isGivenKind(\T_COMMENT)) {
                $newContent = Preg::replace('/^\\/\\*{2,}(?!\\/)/', '/*', $newContent);
            }
            // Fix closing
            $newContent = Preg::replace('/(?<!\\/)\\*{2,}\\/$/', '*/', $newContent);
            if ($newContent !== $originalContent) {
                $tokens[$index] = new Token([$token->getId(), $newContent]);
            }
        }
    }
}
