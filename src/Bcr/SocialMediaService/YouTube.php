<?php

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_SearchResult;

class YouTube implements SocialMediaServiceInterface
{
    private $apiClient;
    private $channelId;

    public function __construct(
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

    /**
     * @return ListItem[]
     */
    public function getList(): array
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
            function (Google_Service_YouTube_SearchResult $item) {
                return ListItem::createFromYoutubeSearchResult($item);
            },
            $response->getItems()
        );
    }
}
