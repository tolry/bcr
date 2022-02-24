<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use Symfony\Contracts\HttpClient\ResponseInterface;
use App\Bcr\Feed\ListItem;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Zend\Feed\Exception\RuntimeException;
use Zend\Feed\Reader\Reader;
use function array_map;
use function iterator_to_array;
use function sha1;
use function sprintf;

class Rss implements SocialMediaServiceInterface
{
    private string $feedUrl;
    private ?ResponseInterface $lazyResponse = null;
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient, string $feedUrl)
    {
        $this->feedUrl    = $feedUrl;
        $this->httpClient = $httpClient;
    }

    public function initializeApiRequest() : void
    {
        $this->lazyResponse = $this->httpClient->request('GET', $this->feedUrl);
    }

    /**
     * @return ListItem[]
     */
    public function getList() : array
    {
        try {
            if (! $this->lazyResponse) {
                $this->initializeApiRequest();
            }

            try {
                $feed = Reader::importString($this->lazyResponse->getContent());
            } catch (RuntimeException $e) {
                return [];
            }

            return array_map(
                fn($item) => ListItem::createFromRssItem($item, $feed->getTitle()),
                [...$feed]
            );
        } catch (RuntimeException | ServerException $e) {
            return [];
        }
    }

    public function getRefreshInterval() : int
    {
        return 5;
    }

    public function getHash() : string
    {
        return sprintf('rss_%s', sha1($this->feedUrl));
    }
}
