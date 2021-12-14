<?php

use PierreMiniggio\MultiSourcesTiktokDownloader\MultiSourcesTiktokDownloader;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$downloader = MultiSourcesTiktokDownloader::buildSelf();
$tikTokUrls = [
    'https://www.tiktok.com/@pierreminiggio/video/6988155668104531205',
    'https://www.tiktok.com/@pierreminiggio/video/6985836381427879174',
    'https://www.tiktok.com/@pierreminiggio/video/6986207453872508165',
    'https://www.tiktok.com/@pierreminiggio/video/6985202474315418885',
    'https://www.tiktok.com/@pierreminiggio/video/6984723104622759173',
    'https://www.tiktok.com/@pierreminiggio/video/6983980673216318726',
    'https://www.tiktok.com/@pierreminiggio/video/6983238803506908422',
    'https://www.tiktok.com/@pierreminiggio/video/6983354440447315205',
    'https://www.tiktok.com/@pierreminiggio/video/6981012184822189317'
];

foreach ($tikTokUrls as $tikTokUrl) {
    $video = $downloader->download($tikTokUrl);
    var_dump($video);
}
