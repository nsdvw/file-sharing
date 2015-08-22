<?php
header('Content-Type: image/jpeg');
session_start();

$captcha = imagecreatefromjpeg('noise.jpg');
$color = imagecolorallocate($captcha, 140, 140, 140);
$letters = 5;
$randomString = strtolower(substr(base64_encode(sha1(uniqid())), 0, $letters));
$_SESSION['captcha'] = $randomString;

$x = 20;
$y = 40;
$offset = 40;

for ($i = 0; $i < $letters; $i++) {
    $size = rand(30, 40);
    $angle = rand(-25, 25);
    imagettftext($captcha, $size, $angle, $x, $y, $color,
        '../fonts/Chewy.ttf',
        $randomString{$i});
    $x += $offset;
}
imagejpeg($captcha);