<?php

declare (strict_types=1);

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Buzz\Browser;
use function array_map;
use function array_reduce;
use function count;
use function end;
use function json_decode;
use function sprintf;
use function str_replace;
use function urlencode;

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
    public function getList() : array
    {
        $browser    = new Browser();
        $url        = sprintf(
            'https://www.flickr.com/services/feeds/photos_public.gne?id=%s&lang=en-us&format=json&nojsoncallback=1',
            urlencode($this->userId)
        );
        $response   = $browser->get($url);
        $jsonString = str_replace("\\'", "'", (string) $response->getContent());

        $data = json_decode($jsonString, true);

        return array_reduce(
            array_map(
                static function ($item) {
                    return ListItem::createFromFlickrItem($item);
                },
                $data['items']
            ),
            static function (array $carry, ListItem $item) {
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
