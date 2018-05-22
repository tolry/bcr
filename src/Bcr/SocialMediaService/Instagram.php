<?php

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Buzz\Browser;

class Instagram implements SocialMediaServiceInterface
{
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return ListItem[]
     */
    public function getList(): array
    {
        $url = sprintf(
            "https://api.instagram.com/v1/users/self/media/recent/?access_token=%s",
            $this->token
        );

        $browser = new Browser();
        $response = $browser->get($url);

        $data = json_decode($response->getContent(), true);

        return array_map(
            function ($item) {
                return ListItem::createFromInstagramItem($item);
            },
            $data['data']
        );
    }
}
