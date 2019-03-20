<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Bcr\Feed\ListItem;
use function array_map;

class Twitter implements SocialMediaServiceInterface
{
    private $username;
    private $key;
    private $secret;

    public function __construct(string $username, string $key, string $secret)
    {
        $this->username = $username;
        $this->key      = $key;
        $this->secret   = $secret;
    }

    /**
     * @return ListItem[]
     */
    public function getList() : array
    {
        $twitterClient = new TwitterOAuth($this->key, $this->secret);

        return array_map(
            function ($item) {
                return ListItem::createFromTwitterItem($item, $this->username);
            },
            $twitterClient->get('/statuses/user_timeline', ['screen_name' => $this->username])
        );
    }
}
