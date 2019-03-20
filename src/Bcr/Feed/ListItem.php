<?php

declare(strict_types=1);

namespace App\Bcr\Feed;

use App\Bcr\Channel;
use DateTime;
use Google_Service_YouTube_SearchResult;
use Zend\Feed\Reader\Entry\AbstractEntry;
use function json_decode;
use function json_encode;
use function property_exists;
use function sha1;
use function uniqid;

class ListItem
{
    public $id;
    public $link;
    public $images = [];
    public $audio  = [];
    public $title;
    public $description;
    public $published;
    public $channel;
    public $debugInfo;
    public $videoProperties = [];

    public function __construct(
        string $id,
        string $link,
        ?string $title,
        ?string $description,
        DateTime $published,
        Channel $channel,
        array $debugInfo = []
    ) {
        $this->id          = $id;
        $this->link        = $link;
        $this->title       = $title;
        $this->description = $description;
        $this->published   = $published;
        $this->channel     = $channel;
        $this->debugInfo   = $debugInfo;
    }

    public function addImage(string $url, ?string $label = null, ?string $thumbnail = null, ?string $link = null) : void
    {
        $this->images[] = [
            'url' => $url,
            'thumbnail' => $thumbnail,
            'label' => $label,
            'link' => $link,
        ];
    }

    public function addAudio(string $url) : void
    {
        $this->audio[] = ['url' => $url];
    }

    public function setVideoProperties(array $props) : void
    {
        $this->videoProperties = $props;
    }

    public static function createFromFlickrItem(array $item) : self
    {
        $instance = new self(
            $item['link'] ?? uniqid(),
            $item['link'] ?? '',
            null,
            null,
            new DateTime($item['published']),
            Channel::flickr('dragonito'),
            $item
        );

        if (isset($item['media']['m'])) {
            $instance->addImage($item['media']['m'], $item['title'], null, $item['link']);
        }

        return $instance;
    }

    public static function createFromInstagramItem(array $item) : self
    {
        $instance = new self(
            $item['link'] ?? uniqid(),
            $item['link'] ?? '',
            null,
            $item['caption']['text'] ?? '',
            new DateTime('@' . $item['created_time']),
            Channel::instagram('dragonito'),
            $item
        );

        if (isset($item['carousel_media'])) {
            foreach ($item['carousel_media'] as $media) {
                if (! isset($media['images']['standard_resolution']['url'])) {
                    continue;
                }

                $instance->addImage(
                    $media['images']['standard_resolution']['url'],
                    null,
                    $media['images']['thumbnail']['url']
                );
            }
        } elseif (isset($item['images']['standard_resolution']['url'])) {
            $instance->addImage($item['images']['standard_resolution']['url']);
        }

        return $instance;
    }

    public static function createFromYoutubeSearchResult(Google_Service_YouTube_SearchResult $item) : self
    {
        $instance = new self(
            $item->getId()->getVideoId(),
            'https://www.youtube.com/watch?v=' . $item->getId()->getVideoId(),
            $item->getSnippet()->getTitle(),
            $item->getSnippet()->getDescription(),
            new DateTime($item->getSnippet()->getPublishedAt()),
            Channel::youtube('robinwillig'),
            json_decode(json_encode($item->toSimpleObject()), true)
        );
        $instance->addImage("https://i.ytimg.com/vi/{$item->getId()->getVideoId()}/maxresdefault.jpg");
        $instance->setVideoProperties(['type' => 'youtube', 'videoId' => $instance->id]);

        return $instance;
    }

    public static function createFromTwitterItem($item, string $username) : self
    {
        $instance = new self(
            $item->id_str ?? uniqid(),
            "https://twitter.com/$username/status/" . $item->id_str,
            null,
            $item->text ?? '',
            new DateTime($item->created_at),
            Channel::twitter($username),
            json_decode(json_encode($item), true)
        );

        if (isset($item->extended_entities->media[0])) {
            foreach ($item->extended_entities->media as $media) {
                $instance->addImage($media->media_url_https);

                if (! property_exists($media, 'video_info')) {
                    continue;
                }

                $instance->setVideoProperties([
                    'type' => 'video',
                    'url' => $media->video_info->variants[0]->url,
                ]);
            }
        }
        return $instance;
    }

    public static function createFromRssItem(AbstractEntry $item, string $feedTitle) : self
    {
        $instance = new self(
            sha1($item->getId()),
            $item->getLink() ?? '',
            $item->getTitle(),
            $item->getDescription(),
            $item->getDateModified() ?? new DateTime(),
            Channel::rss($feedTitle),
            json_decode(json_encode($item), true)
        );

        // check itunes-podcast

        $xpath = $item->getXpath();
        $xpath->registerNamespace('itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
        $prefix = $item->getXpathPrefix();

        $summary = $xpath->evaluate("string($prefix/itunes:summary[1])");
        if ($summary) {
            $instance->description = $summary;
        }

        $image = $xpath->evaluate("string($prefix/itunes:image[1]/@href)");
        if ($image) {
            $instance->addImage($image);
        }

        $audio = $xpath->evaluate("string($prefix/enclosure[1]/@url)");
        if ($audio) {
            $instance->addAudio($audio);
        }

        return $instance;
    }
}
