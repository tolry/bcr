<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Symfony\Component\HttpClient\CurlHttpClient;
use function array_map;
use function sprintf;
use function urlencode;

class Instagram implements SocialMediaServiceInterface
{
    private $token;
    private $lazyResponse;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function initializeApiRequest() : void
    {
        $client = new CurlHttpClient();
        $url    = sprintf(
            'https://api.instagram.com/v1/users/self/media/recent/?access_token=%s',
            urlencode($this->token)
        );

        $this->lazyResponse = $client->request('GET', $url);
    }

    /**
     * @return ListItem[]
     */
    public function getList() : array
    {
        if (! $this->lazyResponse) {
            $this->initializeApiRequest();
        }

        $data = $this->lazyResponse->toArray();

        return array_map(
            static function ($item) {
                return ListItem::createFromInstagramItem($item);
            },
            $data['data']
        );
    }
}
