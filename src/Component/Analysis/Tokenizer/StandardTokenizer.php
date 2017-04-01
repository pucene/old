<?php

namespace Pucene\Component\Analysis\Tokenizer;

use Pucene\Component\Analysis\Token;

class StandardTokenizer implements TokenizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function tokenize($input)
    {
        $tokens = [];

        $start = 0;
        $position = 0;
        $term = '';
        for ($i = 0, $length = strlen($input); $i < $length; ++$i) {
            if (preg_match('/[a-zA-Z0-9\']/', $input[$i])) {
                $term .= $input[$i];

                continue;
            }

            if (strlen($term) > 0) {
                $tokens[] = new Token($term, $start, $i, '<ALPHANUM>', $position);
                ++$position;
            }

            $start = $i + 1;
            $term = '';
        }

        if (strlen($term) > 0) {
            $tokens[] = new Token($term, $start, $i - 1, '<ALPHANUM>', $position);
        }

        return $tokens;
    }
}
