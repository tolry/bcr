<?php

declare(strict_types=1);

namespace App\Bcr\Feed;

use App\Bcr\SocialMediaService\SocialMediaServiceInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use function get_class;
use function sprintf;

class Cache
{
    private \Psr\Cache\CacheItemPoolInterface $cache;
    private \Psr\Log\LoggerInterface $logger;

    public function __construct(CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        $this->cache  = $cache;
        $this->logger = $logger;
    }

    /** @return ListItem[] */
    public function get(SocialMediaServiceInterface $feed) : array
    {
        try {
            $item = $this->cache->getItem($this->key($feed->getHash()));

            return $item->isHit() ? $item->get() : [];
        } catch (Throwable $e) {
            $this->logger->critical(sprintf(
                'exception %s, msg: %s, stacktrace: %s',
                get_class($e),
                $e->getMessage(),
                $e->getTraceAsString()
            ));

            return [];
        }
    }

    /** @param ListItem[] $items */
    public function set(array $items, string $hash, int $expirationSeconds = 300) : void
    {
        try {
            $cacheItem = $this->cache->getItem($this->key($hash));

            $cacheItem->set($items);
            $cacheItem->expiresAfter($expirationSeconds);

            $this->cache->save($cacheItem);
        } catch (Throwable $e) {
            $this->logger->critical(sprintf(
                'exception %s, msg: %s, stacktrace: %s',
                get_class($e),
                $e->getMessage(),
                $e->getTraceAsString()
            ));
        }
    }

    private function key(string $hash) : string
    {
        return 'feed_' . $hash;
    }
}
