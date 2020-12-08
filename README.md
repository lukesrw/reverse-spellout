# reverse-spellout

## Functions

### numberToText

Wrapper for the NumberFormatter class (removes hyphens, commas, include various 'and').

### textToNumber

Converts text (from numberToText) back into numbers, handles any size number.

## Usage

You can use these functions to convert between numbers and text:

```php
<?php

$number = 1234567890;

echo $number . "\n\n";

echo numberToText($number) . "\n\n";

echo textToNumber(numberToText($number));
```

The above code will output:

```txt
1234567890

one billion, two hundred and thirty-four million, five hundred and sixty-seven thousand, eight hundred and ninety

1234567890
```

### Options

You can pass an array of options to the numberToText function as the second argument.

If you pass only some options in the array, or no array at all - the default values from the table below are used.

```php
<?php

$number = 1234567890;

echo numberToText(
    $number,
    array(
        'insert_ands' => 'one'
    )
);
```

| Option         | Description                                             | Values                                                                                                               | Default  |
| -------------- | ------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------- | -------- |
| locale         | Locale used for `\NumberFormatter`                      | [IANA Language Subtags Registry](https://www.iana.org/assignments/language-subtag-registry/language-subtag-registry) | `'en'`   |
| insert_ands    | Number of "and" segments to include                     | `'one'`, `'many'`, `false`                                                                                           | `'many'` |
| insert_commas  | Whether to include commas after thousand/-illian tokens | `true`, `false`                                                                                                      | `true`   |
| remove_hyphens | Remove hyphens from text (i.e. "thirty-four")           | `true`, `false`                                                                                                      | `false`  |
