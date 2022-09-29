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
namespace EbayVendor\PhpCsFixer\Tests\Test;

use EbayVendor\PhpCsFixer\AccessibleObject\AccessibleObject;
use EbayVendor\PhpCsFixer\Tokenizer\Token;
use EbayVendor\PhpCsFixer\Tokenizer\Tokens;
use EbayVendor\PhpCsFixer\Tokenizer\Transformers;
class TokensWithObservedTransformers extends Tokens
{
    /**
     * @var null|string
     */
    public $currentTransformer;
    public $observedModificationsPerTransformer = [];
    public function offsetSet($index, $newval)
    {
        if (null !== $this->currentTransformer) {
            $this->observedModificationsPerTransformer[$this->currentTransformer][] = $this->extractTokenKind($newval);
        }
        parent::offsetSet($index, $newval);
    }
    /**
     * @internal
     */
    protected function applyTransformers()
    {
        $this->observedModificationsPerTransformer = [];
        $transformers = Transformers::createSingleton();
        foreach (AccessibleObject::create($transformers)->items as $transformer) {
            $this->currentTransformer = $transformer->getName();
            $this->observedModificationsPerTransformer[$this->currentTransformer] = [];
            foreach ($this as $index => $token) {
                $transformer->process($this, $token, $index);
            }
        }
        $this->currentTransformer = null;
    }
    /**
     * @param array|string|Token $token token prototype
     *
     * @return int|string
     */
    private function extractTokenKind($token)
    {
        return $token instanceof Token ? $token->isArray() ? $token->getId() : $token->getContent() : (\is_array($token) ? $token[0] : $token);
    }
}
