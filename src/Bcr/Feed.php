<?php

declare(strict_types=1);

namespace App\Bcr;

use App\Bcr\Feed\Cache;
use App\Bcr\Feed\ListItem;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function array_merge;

class Feed
{
    private Configuration $configuration;
    private Cache $cache;

    public function __construct(Configuration $configuration, Cache $cache)
    {
        $this->configuration = $configuration;
        $this->cache         = $cache;
    }

    /** @return ListItem[] */
    public function fetchItems() : array
    {
        $items          = [];
        $nonCachedFeeds = [];

        foreach ($this->configuration->getAllFeeds() as $feed) {
            $cachedItems = $this->cache->get($feed);
            if (! empty($cachedItems)) {
                $items = array_merge($items, $cachedItems);

                continue;
            }

            $feed->initializeApiRequest();
            $nonCachedFeeds[] = $feed;
        }

        foreach ($nonCachedFeeds as $feed) {
            try {
                $newItems = $feed->getList();
            } catch (TransportExceptionInterface $e) {
                if (class_exists(\Tideways\Profiler)) {
                    \Tideways\Profiler::logException($e);
                }

                $newItems = [];
            }

            $this->cache->set($newItems, $feed->getHash());

            $items = array_merge($items, $newItems);
        }

        return $items;
    }
}
