<?php
// Redimensiona e comprime as imagens em public/img (hero, og, portfolio) para uso web.
// Uso: php scripts/optimize-images.php

$targets = [
    __DIR__ . '/../public/img/hero.jpg' => 2000,
    __DIR__ . '/../public/img/og.jpg' => 1200,
    __DIR__ . '/../public/img/portfolio/01.jpg' => 1400,
    __DIR__ . '/../public/img/portfolio/02.jpg' => 1400,
    __DIR__ . '/../public/img/portfolio/03.jpg' => 1400,
    __DIR__ . '/../public/img/portfolio/04.jpg' => 1400,
    __DIR__ . '/../public/img/portfolio/05.jpg' => 1400,
    __DIR__ . '/../public/img/portfolio/06.jpg' => 1400,
];

foreach ($targets as $path => $maxWidth) {
    if (!file_exists($path)) {
        echo "skip (nao existe): $path\n";
        continue;
    }

    $info = getimagesize($path);
    [$width, $height] = $info;

    $src = imagecreatefromjpeg($path);
    $src = imagerotateFromExif($path, $src);

    $width = imagesx($src);
    $height = imagesy($src);

    if ($width > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = (int) round($height * ($maxWidth / $width));
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $before = filesize($path);
    imagejpeg($dst, $path, 80);
    $after = filesize($path);

    imagedestroy($src);
    imagedestroy($dst);

    printf("%s: %dx%d -> %dx%d | %.1fMB -> %.1fMB\n",
        basename($path), $width, $height, $newWidth, $newHeight,
        $before / 1048576, $after / 1048576);
}

function imagerotateFromExif(string $path, $image)
{
    if (!function_exists('exif_read_data')) {
        return $image;
    }

    $exif = @exif_read_data($path);
    if (!$exif || empty($exif['Orientation'])) {
        return $image;
    }

    switch ($exif['Orientation']) {
        case 3:
            return imagerotate($image, 180, 0);
        case 6:
            return imagerotate($image, -90, 0);
        case 8:
            return imagerotate($image, 90, 0);
        default:
            return $image;
    }
}
