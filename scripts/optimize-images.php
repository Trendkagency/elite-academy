<?php

// Optimize images in public/images
$dir = __DIR__ . '/../public/images';
$files = glob($dir . '/*.{png,jpg,jpeg}', GLOB_BRACE);

echo "Starting Image Optimization in: " . realpath($dir) . "\n";
$totalOriginal = 0;
$totalWebp = 0;

foreach ($files as $file) {
    $info = pathinfo($file);
    $ext = strtolower($info['extension']);
    $filename = $info['filename'];
    $originalSize = filesize($file);
    $totalOriginal += $originalSize;

    $raw = file_get_contents($file);
    $img = @imagecreatefromstring($raw);
    if (!$img) {
        echo "Failed to load: " . basename($file) . "\n";
        continue;
    }
    imagepalettetotruecolor($img);
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $width = imagesx($img);
    $height = imagesy($img);

    // 1. Export WebP at full size with quality 80
    $webpPath = $dir . '/' . $filename . '.webp';
    imagewebp($img, $webpPath, 80);
    $webpSize = filesize($webpPath);
    $totalWebp += $webpSize;

    $pct = round((1 - ($webpSize / $originalSize)) * 100, 1);
    echo sprintf("Converted %s: %s KB -> %s KB (-%s%%)\n", basename($file), round($originalSize/1024), round($webpSize/1024), $pct);

    // 2. Generate small thumbnails for avatar/instructor images if applicable
    if (str_contains($filename, 'instructor') || str_contains($filename, 'student')) {
        $thumbSizes = [128, 256];
        foreach ($thumbSizes as $ts) {
            $thumbPath = $dir . '/' . $filename . '_' . $ts . '.webp';
            $thumbImg = imagecreatetruecolor($ts, $ts);
            imagealphablending($thumbImg, false);
            imagesavealpha($thumbImg, true);
            imagecopyresampled($thumbImg, $img, 0, 0, 0, 0, $ts, $ts, $width, $height);
            imagewebp($thumbImg, $thumbPath, 80);
            imagedestroy($thumbImg);
            echo sprintf("  -> Created Thumbnail %s_%s.webp (%s KB)\n", $filename, $ts, round(filesize($thumbPath)/1024));
        }
    }

    // 3. Generate logo optimized versions
    if ($filename === 'logo') {
        $logoThumbPath = $dir . '/logo_500.webp';
        $targetW = 500;
        $targetH = (int) round(($height / $width) * $targetW);
        $logoImg = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($logoImg, false);
        imagesavealpha($logoImg, true);
        imagecopyresampled($logoImg, $img, 0, 0, 0, 0, $targetW, $targetH, $width, $height);
        imagewebp($logoImg, $logoThumbPath, 85);
        imagedestroy($logoImg);
        echo sprintf("  -> Created Resized Logo logo_500.webp (%s KB)\n", round(filesize($logoThumbPath)/1024));
    }

    imagedestroy($img);
}

$savedTotal = $totalOriginal - $totalWebp;
echo "\n============================================\n";
echo sprintf("TOTAL ORIGINAL SIZE: %s MB\n", round($totalOriginal / 1048576, 2));
echo sprintf("TOTAL WEBP SIZE:     %s MB\n", round($totalWebp / 1048576, 2));
echo sprintf("TOTAL BANDWIDTH SAVED: %s MB (-%s%%)\n", round($savedTotal / 1048576, 2), round(($savedTotal / $totalOriginal) * 100, 1));
echo "============================================\n";
