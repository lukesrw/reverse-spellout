<?php
function numberToText($number) {
    $NumberFormatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);

    return strtr(
        $NumberFormatter->format($number),
        array(
            '-' => ' ',
            'hundred ' => 'hundred and ',
            ',' => ''
        )
    );
}

function textToNumber($text) {
    $tokens = '/\w+ion|thousand/';
    $lookup = array(
        'zero' => 0,
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
        'ten' => 10,
        'eleven' => 11,
        'twelve' => 12,
        'thirteen' => 13,
        'fourteen' => 14,
        'fifteen' => 15,
        'sixteen' => 16,
        'seventeen' => 17,
        'eighteen' => 18,
        'nineteen' => 19,
        'twenty' => 20,
        'thirty' => 30,
        'forty' => 40,
        'fourty' => 40,
        'fifty' => 50,
        'sixty' => 60,
        'seventy' => 70,
        'eighty' => 80,
        'ninety' => 90,
        'thousand' => 3,
        'million' => 6,
        'billion' => 9,
        'trillion' => 12,
        'quadrillion' => 15,
        'quintillion' => 18,
        'sextillion' => 21,
        'septillion' => 24,
        'octillion' => 27,
        'nonillion' => 30,
        'decillion' => 33,
        'undecillion' => 36,
        'duodecillion' => 39,
        'tredecillion' => 42,
        'quattuordecillion' => 45,
        'quindecillion' => 48,
        'sexdecillion' => 51,
        'septendecillion' => 54,
        'octodecillion' => 57,
        'novemdecillion' => 60,
        'vigintillion' => 63,
        'centillion' => 303
    );

    $text = strtr(
        strtolower($text),
        array(
            'hundred and ' => 'hundred '
        )
    );

    preg_match_all($tokens, $text, $word_tokens);
    $word_segments = preg_split($tokens, $text);

    return array_reduce(
        array_map(
            function ($segment, $segment_i) use ($word_tokens, $lookup) {
                $segment = explode('hundred', trim($segment));
                if (count($segment) == 1) {
                    array_unshift($segment, '');
                }

                return array_reduce(
                    array_map(
                        function ($segment_part, $segment_part_i) use ($lookup) {
                            return array_reduce(
                                array_map(
                                    function ($segment_part_word) use ($lookup) {
                                        return $lookup[$segment_part_word] ?? $segment_part_word;
                                    },
                                    explode(' ', trim($segment_part))
                                ),
                                function ($total, $value) {
                                    return $total + ($value ?: 0);
                                }
                            ) * ($segment_part_i ?: 100);
                        },
                        $segment,
                        array_keys($segment)
                    ),
                    function ($total, $value) {
                        return $total + ($value ?: 0);
                    }
                ) * pow(10, $lookup[$word_tokens[0][$segment_i] ?? 'zero']);
            },
            $word_segments,
            array_keys($word_segments)
        ),
        function ($total, $value) {
            return $total + $value;
        }
    );
}