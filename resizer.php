<?php
function resizer($source, $destination, $size, $quality = null)
{
// $source - Original image file
// $destination - Resized image file name
// $size - Single number for percentage resize
// Array of 2 numbers for fixed width + height
// $quality - Optional image quality. JPG & WEBP = 0 to 100, PNG = -1 to 9

    // (A) FILE CHECKS
    // Allowed image file extensions
    $ext = strtolower(pathinfo($source)['extension']);
    if (!in_array($ext, ["bmp", "gif", "jpg", "jpeg", "png", "webp"])) {
        throw new Exception('Invalid image file type');
    }

    // Source image not found!
    if (!file_exists($source)) {
        throw new Exception('Source image file not found');
    }

    // (B) IMAGE DIMENSIONS
    $dimensions = getimagesize($source);
    $width = $dimensions[0];
    $height = $dimensions[1];

    if (is_array($size)) {
        $new_width = $size[0];
        $new_height = $size[1];
    } else {
        $new_width = ceil(($size / 100) * $width);
        $new_height = ceil(($size / 100) * $height);
    }

    // (C) RESIZE
    // Respective PHP image functions
    $fnCreate = "imagecreatefrom" . ($ext == "jpg" ? "jpeg" : $ext);
    $fnOutput = "image" . ($ext == "jpg" ? "jpeg" : $ext);

    // Image objects
    $original = $fnCreate($source);
    $resized = imagecreatetruecolor($new_width, $new_height);

    // Transparent images only
    if ($ext == "png" || $ext == "gif") {
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagefilledrectangle(
            $resized, 0, 0, $new_width, $new_height,
            imagecolorallocatealpha($resized, 255, 255, 255, 127)
        );
    }

    // Copy & resize
    imagecopyresampled(
        $resized, $original, 0, 0, 0, 0,
        $new_width, $new_height, $width, $height
    );

    // (D) OUTPUT & CLEAN UP
    if (is_numeric($quality)) {
        $fnOutput($resized, $destination, $quality);
    } else {
        $fnOutput($resized, $destination);
    }
    imagedestroy($original);
    imagedestroy($resized);
}

// (EX) EXAMPLE USAGE
// Percentage resize
resizer("Acate.jpg", "resized-A.jpg", 50);
resizer("Bdoge.png", "resized-B.png", 25);

// Fixed dimension resize + quality
resizer("Acate.jpg", "resized-AA.jpg", [200, 400], 20);
resizer("Bdoge.png", "resized-BB.png", [300, 250], 1);