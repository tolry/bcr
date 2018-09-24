<?php

namespace App\Bcr\Feed;

use App\Bcr\Channel;
use \DateTime;
use Google_Service_YouTube_SearchResult;

class ListItem
{
    public $id;
    public $link;
    public $image;
    public $title;
    public $description;
    public $published;
    public $channel;
    public $debugInfo;

    public function __construct(
        string $id,
        string $link,
        ?string $image,
        ?string $title,
        ?string $description,
        DateTime $published,
        Channel $channel,
        array $debugInfo = []
    ) {
        $this->id = $id;
        $this->link = $link;
        $this->image = $image;
        $this->title = $title;
        $this->description = $description;
        $this->published = $published;
        $this->channel = $channel;
        $this->debugInfo = $debugInfo;
    }

    public static function createFromFlickrItem(array $item): self
    {
        return new self(
            $item['link'] ?? uniqid(),
            $item['link'] ?? '',
            $item['media']['m'] ?? '',
            null,
            $item['title'] ?? '',
            new \DateTime($item['published']),
            Channel::flickr('dragonito'),
            $item
        );
    }

    public static function createFromInstagramItem(array $item): self
    {
        return new self(
            $item['link'] ?? uniqid(),
            $item['link'] ?? '',
            $item['images']['standard_resolution']['url'] ?? '',
            null,
            $item['caption']['text'] ?? '',
            new \DateTime('@' . $item['created_time']),
            Channel::instagram('dragonito'),
            $item
        );
    }

    public static function createFromYoutubeSearchResult(Google_Service_YouTube_SearchResult $item): self
    {
        return new self(
            $item->getId()->getVideoId(),
            'https://www.youtube.com/watch?v=' . $item->getId()->getVideoId(),
            "https://i.ytimg.com/vi/{$item->getId()->getVideoId()}/maxresdefault.jpg",
            $item->getSnippet()->getTitle(),
            $item->getSnippet()->getDescription(),
            new \DateTime($item->getSnippet()->getPublishedAt()),
            Channel::youtube('robinwillig'),
            json_decode(json_encode($item->toSimpleObject()), true)
        );
    }

    public static function createFromTwitterItem($item, string $username): self
    {
        return new self(
            $item->id_str ?? uniqid(),
            "https://twitter.com/$username/status/" . $item->id_str,
            null,
            null,
            $item->text ?? '',
            new \DateTime($item->created_at),
            Channel::twitter($username),
            json_decode(json_encode($item), true)
        );
    }
}
