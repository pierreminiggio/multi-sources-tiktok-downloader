<?php

use PierreMiniggio\MultiSourcesTiktokDownloader\MultiSourcesTiktokDownloader;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$downloader = MultiSourcesTiktokDownloader::buildSelf();
$video = $downloader->download('https://www.tiktok.com/@pierreminiggio/video/6988155668104531205');
var_dump($video);