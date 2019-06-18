<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_SearchResult;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_map;

class YouTube implements SocialMediaServiceInterface
{
    private $apiClient;
    private $channelId;

    public function __construct(
        HttpClientInterface $httpClient,
        string $apiKey,
        string $clientId,
        string $clientSecret,
        string $channelId
    ) {
        $this->apiClient = new Google_Service_YouTube(new Google_Client([
            'developer_key' => $apiKey,
            'client_id' => $clientId,
            'client_secrect' => $clientSecret,
        ]));
        $this->channelId = $channelId;
    }

    public function initializeApiRequest() : void
    {
    }

    /**
     * @return ListItem[]
     */
    public function getList() : array
    {
        $response = $this->apiClient->search->listSearch(
            'snippet, id',
            [
                'channelId' => $this->channelId,
                'maxResults' => 20,
                'type' => 'video',
                'order' => 'date',
            ]
        );

        return array_map(
            static function (Google_Service_YouTube_SearchResult $item) {
                return ListItem::createFromYoutubeSearchResult($item);
            },
            $response->getItems()
        );
    }
}
