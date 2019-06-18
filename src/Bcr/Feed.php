<?php

declare(strict_types=1);

namespace App\Bcr;

use App\Bcr\Feed\ListItem;
use App\Bcr\SocialMediaService\SocialMediaServiceInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use function array_merge;
use function get_class;
use function sprintf;

class Feed
{
    /** @var Configuration */
    private $configuration;
    /** @var LoggerInterface */
    private $logger;
    /** @var CacheItemPoolInterface */
    private $cache;

    public function __construct(
        Configuration $configuration,
        LoggerInterface $logger,
        CacheItemPoolInterface $cache
    ) {
        $this->configuration = $configuration;
        $this->logger        = $logger;
        $this->cache         = $cache;
    }

    /** @return ListItem[] */
    public function fetchItems() : array
    {
        $feeds          = $this->configuration->getAllFeeds();
        $items          = [];
        $nonCachedFeeds = [];
        foreach ($feeds as $hash => $feed) {
            $cachedItems = $this->getItemsCached($feed, $hash);
            if (! empty($cachedItems)) {
                $items = array_merge($items, $cachedItems);

                continue;
            }

            $feed->initializeApiRequest();
            $nonCachedFeeds[$hash] = $feed;
        }

        foreach ($nonCachedFeeds as $hash => $feed) {
            $newItems = $feed->getList();
            $this->cacheItems($newItems, $hash);

            $items = array_merge($items, $newItems);
        }

        return $items;
    }

    /** @return ListItem[] */
    public function getItemsCached(SocialMediaServiceInterface $feed, string $hash) : array
    {
        try {
            $key  = 'feed_' . $hash;
            $item = $this->cache->getItem($key);

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
    public function cacheItems(array $items, string $hash) : void
    {
        try {
            $key  = 'feed_' . $hash;
            $item = $this->cache->getItem($key);

            if (! $item->isHit()) {
                $item->set($items);
                $item->expiresAfter(300);
                $this->cache->save($item);
            }
        } catch (Throwable $e) {
            $this->logger->critical(sprintf(
                'exception %s, msg: %s, stacktrace: %s',
                get_class($e),
                $e->getMessage(),
                $e->getTraceAsString()
            ));
        }
    }
}
