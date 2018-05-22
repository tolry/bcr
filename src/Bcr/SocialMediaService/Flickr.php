<?php

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Buzz\Browser;

class Flickr implements SocialMediaServiceInterface
{
    private $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return ListItem[]
     */
    public function getList(): array
    {
        $browser = new Browser();
        $url = "https://www.flickr.com/services/feeds/photos_public.gne?id={$this->userId}&lang=en-us&format=json&nojsoncallback=1";
        $response = $browser->get($url);
        $jsonString = str_replace("\\'", "'", (string) $response->getContent());

        $data = json_decode($jsonString, true);

        return array_map(
            function ($item) {
                return ListItem::createFromFlickrItem($item);
            },
            $data['items']
        );
    }
}
