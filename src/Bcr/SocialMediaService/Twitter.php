<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Bcr\Feed\ListItem;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_map;
use function sprintf;

class Twitter implements SocialMediaServiceInterface
{
    private string $username;
    private string $key;
    private string $secret;

    public function __construct(HttpClientInterface $httpClient, string $username, string $key, string $secret)
    {
        $this->username = $username;
        $this->key      = $key;
        $this->secret   = $secret;
    }

    public function initializeApiRequest() : void
    {
    }

    /**
     * @return ListItem[]
     */
    public function getList() : array
    {
        $twitterClient = new TwitterOAuth($this->key, $this->secret);

        return array_map(
            fn($item) => ListItem::createFromTwitterItem($item, $this->username),
            $twitterClient->get('/statuses/user_timeline', ['screen_name' => $this->username])
        );
    }

    public function getHash() : string
    {
        return sprintf('twitter_%s', $this->username);
    }

    public function getRefreshInterval() : int
    {
        return 15;
    }
}
