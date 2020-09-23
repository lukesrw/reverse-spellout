# Functions

## numberToText

Wrapper for the NumberFormatter class (removes hyphens, commas, include various 'and').

## textToNumber

Converts text (from numberToText) back into numbers, handles any size number.

# Usage

You can use these functions to convert between numbers and text:

```php
<?php

$number = 1234567890;

echo $number . "\n\n";

echo numberToText($number) . "\n\n";

echo textToNumber(numberToText($number));

?>
```

The above code will output:

```
1234567890

one billion two hundred and thirty four million five hundred and sixty seven thousand eight hundred and ninety

1234567890
```
