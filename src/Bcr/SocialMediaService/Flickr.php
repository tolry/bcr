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

        return array_reduce(
            array_map(
                function ($item) {
                    return ListItem::createFromFlickrItem($item);
                },
                $data['items']
            ),
            function (array $carry, ListItem $item) {
               if (count($carry) === 0) {
                   return [$item];
               } 

               $last = end($carry);

               if ($last->published->diff($item->published)->i < 60) {
                    $last->addImage($item->images[0]['url'], $item->images[0]['label'], null, $item->images[0]['link']);
               }

               return $carry;
            },
            []
        );
    }
}
