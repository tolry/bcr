<?php

declare(strict_types=1);

namespace App\Bcr;

use App\Bcr\Feed\Cache;
use App\Bcr\Feed\ListItem;
use App\Bcr\SocialMediaService\SocialMediaServiceInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_merge;

class Feed
{
    private Configuration $configuration;
    private Cache $cache;
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(Configuration $configuration, Cache $cache, HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->configuration = $configuration;
        $this->cache         = $cache;
        $this->httpClient    = $httpClient;
        $this->logger        = $logger;
    }

    /** @return ListItem[] */
    public function fetchItems() : array
    {
        $items          = [];
        $nonCachedFeeds = [];

        foreach ($this->configuration->getAllFeeds($this->httpClient) as $feed) {
            $cachedItems = $this->cache->get($feed);
            if (! empty($cachedItems)) {
                $items = array_merge($items, $cachedItems);

                continue;
            }

            $feed->initializeApiRequest();
            $nonCachedFeeds[] = $feed;
        }

        foreach ($nonCachedFeeds as $feed) {
            assert($feed instanceof SocialMediaServiceInterface);

            try {
                $newItems = $feed->getList();

                $this->cache->set($newItems, $feed->getHash());
            } catch(\Throwable $e) {
                if (class_exists('Tideways\Profiler')) {
                    \Tideways\Profiler::logException($e);
                }

                $this->logger->critical('Failed to fetch Feed', ['feed' => $feed->getHash(), 'exception' => $e]);

                $newItems = [];
            }

            $items = array_merge($items, $newItems);
        }

        return $items;
    }
}
