<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Symfony\Component\HttpClient\CurlHttpClient;
use Zend\Feed\Reader\Reader;
use function array_map;
use function iterator_to_array;

class Rss implements SocialMediaServiceInterface
{
    private $feedUrl;
    private $lazyResponse;

    public function __construct(string $feedUrl)
    {
        $this->feedUrl = $feedUrl;
    }

    public function initializeApiRequest() : void
    {
        $client             = new CurlHttpClient();
        $this->lazyResponse = $client->request('GET', $this->feedUrl);
    }

    /**
     * @return ListItem[]
     */
    public function getList() : array
    {
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
    }
}
