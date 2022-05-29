<?php

namespace PierreMiniggio\MultiSourcesTiktokDownloader;

class Repository
{
    public function __construct(
        public string $token,
        public string $owner,
        public string $repo
    )
    {
    }
}
