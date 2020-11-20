<?php

// Get latest versions of these two files from
// https://unpkg.com/tailwindcss@%5E2.0/dist/tailwind.css
// https://cdn.jsdelivr.net/npm/@tailwindcss/ui@latest/dist/tailwind-ui.css

$classes['tailwind.css'] = extractClasses(file_get_contents('tailwind.css'));
$classes['tailwind-ui.css'] = extractClasses(file_get_contents('tailwind-ui.css'));

function extractClasses($file)
{
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

        // only keep first part, ignore everything after space
        $parts = explode(' ', $match);
        $match = $parts[0];

        $match = stripslashes($match);

        $excludeThesePrefixes = [
            '.sm:',
            '.md:',
            '.lg:',
            '.xl:',
            '.32xl:',

            '.active:',
            '.focus:',
            '.focus-within:',
            '.hover:',

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
            // ' {}',
            // ' > :not(template) ~ :not(template)',
            // ' > :not([hidden]) ~ :not([hidden])',

            '::-moz-placeholder',
            '::-ms-check',
            '::-ms-expand',
            '::-ms-input-placeholder',
            '::-webkit-input-placeholder',
            '::placeholder',

            ':-ms-input-placeholder',
            ':active',
            ':after',
            ':checked',
            ':focus-within',
            ':focus',
            ':hover',
        ];

        foreach ($stripThese as $strip) {
            $match = str_replace($strip, '', $match);
        }

        $keepers[] = sprintf('        \'%s\',', $match);
    }

    return $keepers;
}

$keepers = array_merge($classes['tailwind.css'], $classes['tailwind-ui.css']);

sort($keepers);

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

file_put_contents('src/views/tailwind-classes.js', $file);
