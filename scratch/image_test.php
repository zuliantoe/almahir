<?php
$path = 'c:/laragon/www/almahir/public/logo.png';
if (!file_exists($path)) {
    echo "File does not exist\n";
    exit;
}
$im = imagecreatefrompng($path);
$rgba = imagecolorat($im, 0, 0);
$colors = imagecolorsforindex($im, $rgba);
print_r($colors);
