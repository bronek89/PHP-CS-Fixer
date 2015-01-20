<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Bronisław Białek <after89@gmail.com>
 */
class MultilineArrayValuesAlignment extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens->isArray($index)) {
                $this->fixArray($tokens, $index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP multi-line arrays should have a trailing comma.';
    }

    private function fixArray(Tokens $tokens, $index)
    {
        if (!$tokens->isArrayMultiLine($index)) {
            return;
        }

        $startIndex = $index;

        if ($tokens[$startIndex]->isGivenKind(T_ARRAY)) {
            $startIndex = $tokens->getNextTokenOfKind($startIndex, array('('));
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        } else {
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, $startIndex);
        }
        
        $firstToken = null;
        $i = $startIndex;
        
        while (--$i > 0 && $tokens[$i]->getLine() === $tokens[$startIndex]->getLine()) {
            $firstToken = $tokens[$i];
        }
        
        var_dump($firstToken);
        var_dump($tokens);

        $beforeEndIndex = $endIndex - 1;
        $beforeEndToken = $tokens[$beforeEndIndex];
        
        if ($beforeEndToken->isWhitespace()) {
            $content = $beforeEndToken->getContent();
            $parts = explode("\n", $content);
            
            $beforeEndToken->setContent("\n");
        }
    }
}
