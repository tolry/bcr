<?php

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Zend\Feed\Reader\Reader;

class Rss implements SocialMediaServiceInterface
{
    private $feedUrl;

    public function __construct(string $feedUrl)
    {
        $this->feedUrl = $feedUrl;
    }

    /**
     * @return ListItem[]
     */
    public function getList(): array
    {
        $feed = Reader::import($this->feedUrl);
        return array_map(
            function ($item) use ($feed) {
                return ListItem::createFromRssItem($item, $feed->getTitle());
            },
            iterator_to_array($feed)
        );
    }
}
