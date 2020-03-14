<?php

// Grab contents of css file
// $file = file_get_contents('tailwind.css');
$file = file_get_contents('tailwind-ui.css');

// Strip comments
$pattern = '!/\*[^*]*\*+([^/][^*]*\*+)*/!';
$stripped = preg_replace($pattern, '', $file);

// Strip out everything between { and }
$pattern = '/(?<=\{)(.*?)(?=\})/s';
$stripped = preg_replace($pattern, '', $stripped);

// Remove double line breaks
$stripped = str_replace("\n\n", "\n", $stripped);

// Convert every line to array
$classes = explode("\n", $stripped);

$keepers = [];

for ($i = 0; $i < count($classes); $i++) {
    $match = trim($classes[$i]);

    $match = stripslashes($match);

    $excludeThesePrefixes = [
        '.sm:',
        '.md:',
        '.lg:',
        '.xl:',

        '.form',

        '.group:hover',
        '.group:focus',
    ];

    if (substr($match, 0, 1) !== '.') {
        continue;
    }

    foreach ($excludeThesePrefixes as $exclude) {
        if (strpos($match, $exclude) === 0) {
            continue 2;
        }
    }

    $stripThese = [
        '.',
        ' {}',
        ':focus:-ms-input-placeholder',
        ':focus::-ms-input-placeholder',
        ':focus::placeholder',
        '::placeholder',
        '::-ms-input-placeholder',
        ':-ms-input-placeholder',
        '::-moz-placeholder',
        '::-webkit-input-placeholder',
        ':focus-within',
        ':focus',
    ];

    foreach ($stripThese as $strip) {
        $match = str_replace($strip, '', $match);
    }

    $keepers[] = sprintf('        \'%s\',', $match);
}

$file = '
export default {
  data() {
    return {
      classes: [
' . implode("\n", array_unique($keepers)) . '
      ],
    };
  },
};
';

file_put_contents('tailwind-classes.js', $file);
