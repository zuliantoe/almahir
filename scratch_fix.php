<?php

$file = 'd:/laragon/www/almahir/resources/views/layouts/partials/sidebar.blade.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);

$newLines = [];
$inRemote = false;

foreach ($lines as $line) {
    if (str_starts_with(trim($line), '<<<<<<< HEAD')) {
        continue;
    }
    if (str_starts_with(trim($line), '=======')) {
        $inRemote = true;
        continue;
    }
    if (str_starts_with(trim($line), '>>>>>>>')) {
        $inRemote = false;
        continue;
    }
    if (!$inRemote) {
        $newLines[] = $line;
    }
}

file_put_contents($file, implode("\n", $newLines));
echo "Fixed conflicts in sidebar.blade.php\n";
