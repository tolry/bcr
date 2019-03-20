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
        $items = [];
        foreach ($this->configuration->getAllFeeds() as $hash => $feed) {
            $items = array_merge($items, $this->getItemsCached($feed, $hash));
        }

        return $items;
    }

    /** @return ListItem[] */
    public function getItemsCached(SocialMediaServiceInterface $feed, string $hash) : array
    {
        try {
            $key  = 'feed_$hash';
            $item = $this->cache->getItem($key);

            if (true || ! $item->isHit()) {
                $item->set($feed->getList());
                $item->expiresAfter(300);
                $this->cache->save($item);
            }

            return $item->get();
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
}
