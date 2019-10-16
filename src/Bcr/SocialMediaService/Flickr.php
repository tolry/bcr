<?php

declare (strict_types=1);

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_map;
use function array_reduce;
use function count;
use function end;
use function json_decode;
use function sha1;
use function sprintf;
use function str_replace;
use function urlencode;

class Flickr implements SocialMediaServiceInterface
{
    private $userId;
    private $lazyResponse;
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient, string $userId)
    {
        $this->userId     = $userId;
        $this->httpClient = $httpClient;
    }

    public function initializeApiRequest() : void
    {
        $url = sprintf(
            'https://www.flickr.com/services/feeds/photos_public.gne?id=%s&lang=en-us&format=json&nojsoncallback=1',
            urlencode($this->userId)
        );

        $this->lazyResponse = $this->httpClient->request('GET', $url);
    }

    /**
     * @return ListItem[]
     */
    public function getList() : array
    {
        if (! $this->lazyResponse) {
            $this->initializeApiRequest();
        }

        $jsonString = str_replace("\\'", "'", $this->lazyResponse->getContent());

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

    public function getRefreshInterval() : int
    {
        return 15;
    }

    public function getHash() : string
    {
        return sprintf('flickr_%s', sha1($this->userId));
    }
}
