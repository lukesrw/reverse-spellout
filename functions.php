<?php
$numberToText_and_textToNumber_tokens = '/(\w+ion|thousand)/';
$numberToText_default_options = array(
    'locale' => 'en',
    'insert_ands' => 'many',
    'insert_commas' => true,
    'remove_hyphens' => false
);

/**
 * Format a number "100" into text "one hundred"
 *
 * @param integer|double $number to format into text
 * @param array $options to format number with
 *
 * @return string Formatted number as text
 */
function numberToText($number, $options = null) {
    global $numberToText_default_options;
    global $numberToText_and_textToNumber_tokens;

    $options = array_merge(
        $numberToText_default_options,
        is_array($options) ? $options : $numberToText_default_options
    );

    /**
     * Format the input number, using the given locale
     */
    $NumberFormatter = new \NumberFormatter(
        $options['locale'],
        \NumberFormatter::SPELLOUT
    );
    $text = strtr(
        $NumberFormatter->format($number),
        array(
            ',' => ''
        )
    );

    /**
     * Insert one or many 'and' words into the number text
     */
    switch ($options['insert_ands'] ?? $numberToText_default_options['insert_ands']) {
        case 'many':
            $text = strtr(
                $text,
                array(
                    'hundred ' => 'hundred and '
                )
            );
        break;

        case 'one':
            if (strpos($text, ' hundred ') !== false) {
                $text = explode(' hundred ', $text);
                $text[count($text) - 1] = 'and ' . $text[count($text) - 1];
                $text = implode(' hundred ', $text);
            }
        break;
    }

    /**
     * Insert commas into the number text
     */
    if ($options['insert_commas'] ?? $numberToText_default_options['insert_commas']) {
        $text = preg_replace(
            $numberToText_and_textToNumber_tokens,
            '$1,',
            $text
        );
    }

    /**
     * Remove hyphens from the produced output
     */
    if ($options['remove_hyphens'] ?? $numberToText_default_options['remove_hyphens']) {
        $text = strtr(
            $text,
            array(
                '-' => ' '
            )
        );
    }

    /**
     * Remove trailing comma, if needed
     */
    if (substr($text, -1) == ',') {
        $text = substr(
            $text,
            0,
            strlen($text) - 1
        );
    }

    return $text;
}

/**
 * Format text "one hundred" into a number "100"
 *
 * @param string $text to format into number
 *
 * @return integer|double Formatted text as number
 */
function textToNumber($text) {
    global $numberToText_and_textToNumber_tokens;

    $lookup = array(
        // word and corresponding number
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
        // word and corresponding power
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
            'hundred and ' => 'hundred ',
            ',' => ''
        )
    );

    /**
     * Split $text into an array of tokens, some examples,
     * "one hundred and twenty three thousand three hundred and twenty one":
     *     [["thousand"]]
     *
     * "one hundred and twenty three million four hundred and fifty four thousand three hundred and twenty one":
     *     [["million", "thousand"]]
     */
    preg_match_all($numberToText_and_textToNumber_tokens, $text, $word_tokens);

    /**
     * Split $text into an array without the tokens, some examples,
     * "one hundred and twenty three thousand three hundred and twenty one":
     *     ["one hundred and twenty three", "three hundred and twenty one"]
     *
     * "one hundred and twenty three million four hundred and fifty four thousand three hundred and twenty one":
     *     ["one hundred and twenty three", "four hundred and fifty four", "three hundred and twenty one"]
     */
    $word_segments = preg_split($numberToText_and_textToNumber_tokens, $text);

    return array_reduce(
        array_map(
            function ($segment, $segment_i) use ($word_tokens, $lookup) {
                /**
                 * Split the hundreds from the tens and the ones, some examples:
                 * "one hundred and twenty three":
                 *     ["one", "twenty three"]
                 *
                 * "three hundred and twenty one":
                 *     ["three", "twenty one"]
                 *
                 * "twenty two":
                 *     ["twenty two"]
                 */
                $segment = explode(
                    'hundred',
                    trim($segment)
                );

                /**
                 * Ensure the array has a hundreds part, some examples,
                 * ["one", "twenty three"]:
                 *     ["one", "twenty three"]
                 *
                 * ["twenty two"]:
                 *     ["zero", "twenty two"]
                 */
                if (count($segment) == 1) {
                    array_unshift($segment, 'zero');
                }

                /**
                 * Multiply the calculated reduced number with power of the token
                 * Zero is used to ensure a power of 0 is used at the end of a word
                 */
                return array_reduce(
                    array_map(
                        function ($segment_part, $segment_part_i) use ($lookup) {
                            /**
                             * Calculate the sum of the hundreds and the tens/ones
                             * Then multiply them by 1 or 100, based on the segment part index
                             */
                            return array_reduce(
                                /**
                                 * Split remaining segment by a space to isolate specific words
                                 * Then replace with lookup value to substitude word for number
                                 *
                                 * If no lookup is found, we keep the value - as it's likely a number,
                                 * For example if the function input was "3 million", this would work
                                 */
                                array_map(
                                    function ($segment_part_word) use ($lookup) {
                                        return $lookup[$segment_part_word] ?? $segment_part_word;
                                    },
                                    preg_split(
                                        '/ |-/',
                                        trim($segment_part)
                                    )
                                ),
                                function ($total, $value) {
                                    $value = $value ?: 0;

                                    if (! is_numeric($value)) {
                                        throw new \Exception(sprintf(
                                            'Invalid Reduce, "%1$s"',
                                            $value
                                        ));
                                    }

                                    return $total + $value;
                                }
                            ) * ($segment_part_i ?: 100);
                        },
                        $segment,
                        array_keys($segment)
                    ),
                    function ($total, $value) {
                        return $total + $value;
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