<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use RuntimeException;
use Symfony\Component\HttpClient\CurlHttpClient;

final class Factory
{
    public function create(Type $type, array $options) : SocialMediaServiceInterface
    {
        // @todo validate options array, specific to type

        $httpClient = new CurlHttpClient();

        switch ($type) {
            case Type::TWITTER:
                return new Twitter($httpClient, $options['username'], $options['key'], $options['secret']);
            case Type::YOUTUBE:
                return new YouTube($httpClient, $options['api_key'], $options['client_id'], $options['client_secret'], $options['channel_id']);
            case Type::FLICKR:
                return new Flickr($httpClient, $options['user_id']);
            case Type::INSTAGRAM:
                return new Instagram($httpClient, $options['token']);
            case Type::RSS:
                return new Rss($httpClient, $options['feed_url']);
            default:
                throw new RuntimeException('unsupported type: ' . (string) $type);
        }
    }
}
