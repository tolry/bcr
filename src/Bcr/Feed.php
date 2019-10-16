<?php

declare(strict_types=1);

namespace App\Bcr;

use App\Bcr\Feed\Cache;
use App\Bcr\Feed\ListItem;
use Psr\Cache\CacheItemPoolInterface;
use function array_merge;

class Feed
{
    /** @var Configuration */
    private $configuration;
    /** @var CacheItemPoolInterface */
    private $cache;

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
            $newItems = $feed->getList();
            $this->cache->set($newItems, $feed->getHash());

            $items = array_merge($items, $newItems);
        }

        return $items;
    }
}
