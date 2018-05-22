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

    public function __construct(
        string $id,
        string $link,
        ?string $image,
        string $title,
        ?string $description,
        DateTime $published,
        Channel $channel
    ) {
        $this->id = $id;
        $this->link = $link;
        $this->image = $image;
        $this->title = $title;
        $this->description = $description;
        $this->published = $published;
        $this->channel = $channel;
    }

    public static function createFromFlickrItem(array $item): self
    {
        return new self(
            $item['link'] ?? uniqid(),
            $item['link'] ?? '',
            $item['media']['m'] ?? '',
            $item['title'] ?? '',
            null,
            new \DateTime($item['published']),
            Channel::flickr('dragonito')
        );
    }

    public static function createFromInstagramItem(array $item): self
    {
        return new self(
            $item['link'] ?? uniqid(),
            $item['link'] ?? '',
            $item['images']['standard_resolution']['url'] ?? '',
            $item['caption']['text'] ?? '',
            null,
            new \DateTime('@' . $item['created_time']),
            Channel::instagram('dragonito')
        );
    }

    public static function createFromYoutubeSearchResult(Google_Service_YouTube_SearchResult $item): self
    {
        return new self(
            $item->getId()->getVideoId(),
            'https://www.youtube.com/watch?v=' . $item->getId()->getVideoId(),
            $item->getSnippet()->getThumbnails()->getHigh()->getUrl(),
            $item->getSnippet()->getTitle(),
            $item->getSnippet()->getDescription(),
            new \DateTime($item->getSnippet()->getPublishedAt()),
            Channel::youtube('robinwillig')
        );
    }

    public static function createFromTwitterItem($item, string $username): self
    {
        return new self(
            $item->id_str ?? uniqid(),
            "https://twitter.com/$username/status/" . $item->id_str,
            null,
            $item->text ?? '',
            $item->text ?? '',
            new \DateTime($item->created_at),
            Channel::twitter($username)
        );
    }
}
