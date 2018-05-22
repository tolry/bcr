<?php

namespace App\Bcr;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Bcr\Configuration;
use App\Bcr\Feed\ListItem;
use App\Bcr\SocialMediaService\SocialMediaServiceInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Feed
{
    private $configuration;
    private $logger;
    private $cache;
    private $fileCacheDir;

    public function __construct(
        Configuration $configuration,
        LoggerInterface $logger,
        CacheItemPoolInterface $cache,
        KernelInterface $kernel
    ) {
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->fileCacheDir = $kernel->getCacheDir();
    }

    /**
     * @return ListItem[]
     */
    public function fetchItems(): array
    {
        $items = [];
        foreach ($this->configuration->getAllFeeds() as $hash => $feed) {
            $items = array_merge($items, $this->getItemsCached($feed, $hash));
        }

        return $items;
    }

    public function getItemsCached(SocialMediaServiceInterface $feed, string $hash)
    {
        try {
            $key = "feed_$hash";
            $item = $this->cache->getItem($key);

            if (!$item->isHit()) {
                $item->set($feed->getList());
                $item->expiresAfter(300);
                $this->cache->save($item);
            }

            return $item->get();
        } catch (\Exception $e) {
            $this->logger->critical(sprintf(
                "exception %s, msg: %s, stacktrace: %s",
                get_class($e),
                $e->getMessage(),
                $e->getTraceAsString()
            ));

            return [];
        }
    }

    private function log(string $channel, ?array $data)
    {
        $filename = $this->fileCacheDir . '/' . $channel . '.json';
        file_put_contents($filename, json_encode($data));
    }
}
