<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use Symfony\Contracts\HttpClient\ResponseInterface;
use App\Bcr\Feed\ListItem;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_map;
use function sha1;
use function sprintf;
use function urlencode;

class Instagram implements SocialMediaServiceInterface
{
    private string $token;
    private HttpClientInterface $httpClient;
    private ?ResponseInterface $lazyResponse = null;

    public function __construct(HttpClientInterface $httpClient, string $token)
    {
        $this->token      = $token;
        $this->httpClient = $httpClient;
    }

    public function initializeApiRequest() : void
    {
        $url = sprintf(
            'https://api.instagram.com/v1/users/self/media/recent/?access_token=%s',
            urlencode($this->token)
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

        $data = $this->lazyResponse->toArray();

        return array_map(
            fn($item) => ListItem::createFromInstagramItem($item),
            $data['data']
        );
    }

    public function getRefreshInterval() : int
    {
        return 15;
    }

    public function getHash() : string
    {
        return sprintf('instagram_%s', sha1($this->token));
    }
}
