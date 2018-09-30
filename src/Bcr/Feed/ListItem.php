<?php

namespace App\Bcr\Feed;

use App\Bcr\Channel;
use \DateTime;
use Google_Service_YouTube_SearchResult;

class ListItem
{
    public $id;
    public $link;
    public $images = [];
    public $title;
    public $description;
    public $published;
    public $channel;
    public $debugInfo;

    public function __construct(
        string $id,
        string $link,
        ?string $title,
        ?string $description,
        DateTime $published,
        Channel $channel,
        array $debugInfo = []
    ) {
        $this->id = $id;
        $this->link = $link;
        $this->title = $title;
        $this->description = $description;
        $this->published = $published;
        $this->channel = $channel;
        $this->debugInfo = $debugInfo;
    }

    public function addImage(string $url, ?string $thumbnail = null): self
    {
        $this->images[] = [
            'url' => $url,
            'thumbnail' => $thumbnail
        ];

        return $this;
    }

    public static function createFromFlickrItem(array $item): self
    {
        $instance = new self(
            $item['link'] ?? uniqid(),
            $item['link'] ?? '',
            null,
            $item['title'] ?? '',
            new \DateTime($item['published']),
            Channel::flickr('dragonito'),
            $item
        );

        if (isset($item['media']['m'])) {
            $instance->addImage($item['media']['m']);
        }

        return $instance;
    }

    public static function createFromInstagramItem(array $item): self
    {
        $instance = new self(
            $item['link'] ?? uniqid(),
            $item['link'] ?? '',
            null,
            $item['caption']['text'] ?? '',
            new \DateTime('@' . $item['created_time']),
            Channel::instagram('dragonito'),
            $item
        );

        if (isset($item['carousel_media'])) {
            foreach ($item['carousel_media'] as $media) {
                if (!isset($media['images']['standard_resolution']['url'])) {
                    continue;
                }

                $instance->addImage(
                    $media['images']['standard_resolution']['url'],
                    $media['images']['thumbnail']['url']
                );
            }
        } elseif (isset($item['images']['standard_resolution']['url'])) {
            $instance->addImage($item['images']['standard_resolution']['url']);
        }

        return $instance;
    }

    public static function createFromYoutubeSearchResult(Google_Service_YouTube_SearchResult $item): self
    {
        $instance = new self(
            $item->getId()->getVideoId(),
            'https://www.youtube.com/watch?v=' . $item->getId()->getVideoId(),
            $item->getSnippet()->getTitle(),
            $item->getSnippet()->getDescription(),
            new \DateTime($item->getSnippet()->getPublishedAt()),
            Channel::youtube('robinwillig'),
            json_decode(json_encode($item->toSimpleObject()), true)
        );
        $instance->addImage("https://i.ytimg.com/vi/{$item->getId()->getVideoId()}/maxresdefault.jpg");

        return $instance;
    }

    public static function createFromTwitterItem($item, string $username): self
    {
        return new self(
            $item->id_str ?? uniqid(),
            "https://twitter.com/$username/status/" . $item->id_str,
            null,
            $item->text ?? '',
            new \DateTime($item->created_at),
            Channel::twitter($username),
            json_decode(json_encode($item), true)
        );
    }
}
