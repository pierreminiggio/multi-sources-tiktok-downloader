<?php

namespace PierreMiniggio\MultiSourcesTiktokDownloader;

use Exception;
use PierreMiniggio\AreFilesTheSame\AreFilesTheSame;
use PierreMiniggio\TikTokDownloader\Downloader;
use PierreMiniggio\TikTokDownloader\DownloadFailedException;

class MultiSourcesTiktokDownloader
{

    private string $cacheFolder;

    public function __construct(
        private Downloader $bashDownloader
    )
    {
        $this->cacheFolder = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
    }

    /**
     * @throws Exception
     */
    public function download(string $tikTokUrl): string
    {
        $cacheFolder = $this->cacheFolder;

        if (! file_exists($cacheFolder)) {
            mkdir($cacheFolder);
        }

        $videoFile = $cacheFolder . str_replace(['+','/','='], ['-','_',''], base64_encode($tikTokUrl)) . '.mp4';

        if (file_exists($videoFile)) {
            return $videoFile;
        }

        try {
            $this->bashDownloader->downloadWithoutWatermark($tikTokUrl, $videoFile);
        } catch (DownloadFailedException) {
            try {
                $this->tryGoDownloaderDotCom($tikTokUrl, $videoFile, 3);
            } catch (Exception) {
                throw new Exception('Download failed');
            }
        }

        if (file_exists($videoFile)) {
            return $videoFile;
        }

        throw new Exception('Download failed');
    }

    public static function buildSelf(): self
    {
        return new self(new Downloader());
    }

    /**
     * @throws Exception
     */
    protected function tryGoDownloaderDotCom(string $videoToPostUrl, string $videoFile, int $tries = 1): void
    {
        $tries = $tries - 1;

        $videoInfoCurl = curl_init(
            'https://godownloader.com/api/tiktok-no-watermark-free?url=' . $videoToPostUrl . '&key=godownloader.com'
        );

        curl_setopt_array($videoInfoCurl, [
            CURLOPT_RETURNTRANSFER => 1
        ]);

        $videoInfoCurlResponse = curl_exec($videoInfoCurl);
        curl_close($videoInfoCurl);

        if (empty($videoInfoCurlResponse)) {
            throw new Exception('Empty response');
        }

        $videoInfoCurlJsonResponse = json_decode($videoInfoCurlResponse, true);

        if (empty($videoInfoCurlJsonResponse)) {
            throw new Exception('Empty JSON response');
        }

        if (empty($videoInfoCurlJsonResponse['video_no_watermark'])) {
            throw new Exception('Missing video_no_watermark url');
        }

        $videoNoWatermarkUrl = $videoInfoCurlJsonResponse['video_no_watermark'];

        $fp = fopen($videoFile, 'w+');
        $ch = curl_init($videoNoWatermarkUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $adFolder = __DIR__ . DIRECTORY_SEPARATOR;

        if (
            AreFilesTheSame::areFilesTheSame($videoFile, $adFolder . 'pub.mp4')
            || AreFilesTheSame::areFilesTheSame($videoFile, $adFolder . 'pub2.mp4')
        ) {
            unlink($videoFile);

            if ($tries === 0) {
                throw new Exception('Downloaded file is an ad');
            }

            $this->tryGoDownloaderDotCom($videoToPostUrl, $videoFile, $tries);
        }
    }
}
