<?php
session_start();

$captcha_code = '';
$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
for ($i = 0; $i < 5; $i++) {
    $captcha_code .= $characters[mt_rand(0, strlen($characters) - 1)];
}
$_SESSION['captcha_code'] = $captcha_code;

header('Content-Type: image/png');
$image = imagecreatetruecolor(120, 40);
$background_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
$line_color = imagecolorallocate($image, 64, 64, 64);
$pixel_color = imagecolorallocate($image, 128, 128, 128);

imagefilledrectangle($image, 0, 0, 119, 39, $background_color);

// Add some random lines to make it harder to read
for ($i = 0; $i < 3; $i++) {
    imageline($image, 0, mt_rand(0, 39), 119, mt_rand(0, 39), $line_color);
}

// Add some random pixels
for ($i = 0; $i < 1000; $i++) {
    imagesetpixel($image, mt_rand(0, 119), mt_rand(0, 39), $pixel_color);
}

// Write the CAPTCHA code to the image
imagettftext($image, 20, 0, 15, 30, $text_color, APPROOT . '/public/assets/fonts/arial.ttf', $captcha_code);
// Note: You'll need to add a font file, like 'arial.ttf', to `public/assets/fonts/`

imagepng($image);
imagedestroy($image);
?>