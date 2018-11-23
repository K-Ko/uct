#!/usr/bin/env php
<?php
/**
 * Pipe file contents in to script
 *
 * https://stackoverflow.com/a/19862514
 */

// All or only 2-char languages
$length = isset($argv[1]) ? $argv[1] : 3;

// Let's go
$inLang = $code = $name = false;
$sql = [];

while (!feof(STDIN)) {
    $line = explode(':', fgets(STDIN), 2);

    array_walk($line, function (&$e) {
        $e = trim($e);
    });

    switch (strtolower($line[0])) {
        // -------------------
        case '%%':
            if ($inLang && $code && $code != 'en' && $name && strlen($code) <= $length) {
                // Exclude en
                $sql[] = sprintf(
                    '("code_lang", "en", "%s", "%s", 2, 0)',
                    $code,
                    str_replace('"', '\\"', $name)
                );
            }

            $inLang = $code = $name = false;
            break;

        // -------------------
        case 'type':
            if ($line[1] == 'language') {
                $inLang = true;
            }
            break;

        // -------------------
        case 'subtag':
            if ($inLang) {
                $code = $line[1];
            }
            break;

        // -------------------
        case 'description':
            if ($inLang && $line[1] != 'Not applicable') {
                $name = $line[1];
            }
            break;
    } // switch
}

sort($sql);

echo 'REPLACE INTO `uct` (`set`, `lang`, `code`, `desc`, `order`, `active`) VALUES', PHP_EOL,
     implode(','.PHP_EOL, $sql), ';', PHP_EOL;
