<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Zend\Feed\Reader\Reader;
use function array_map;
use function iterator_to_array;
use function sha1;
use function sprintf;

class Rss implements SocialMediaServiceInterface
{
    private $feedUrl;
    private $lazyResponse;
    private $httpClient;

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

            $feed = Reader::importString($this->lazyResponse->getContent());

            return array_map(
                static function ($item) use ($feed) {
                    return ListItem::createFromRssItem($item, $feed->getTitle());
                },
                iterator_to_array($feed)
            );
        } catch (ServerException $e) {
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
