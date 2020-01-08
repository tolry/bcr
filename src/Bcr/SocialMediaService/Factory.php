<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use RuntimeException;
use Symfony\Component\HttpClient\CurlHttpClient;

final class Factory
{
    private \Symfony\Component\HttpClient\CurlHttpClient $httpClient;

    public function __construct()
    {
        $this->httpClient = new CurlHttpClient();
    }

    /**
     * @param array<string, string> $options
     */
    public function create(Type $type, array $options) : SocialMediaServiceInterface
    {
        // @todo validate options array, specific to type

        switch ($type) {
            case Type::TWITTER:
                return new Twitter($this->httpClient, $options['username'], $options['key'], $options['secret']);
            case Type::YOUTUBE:
                return new YouTube($this->httpClient, $options['api_key'], $options['client_id'], $options['client_secret'], $options['channel_id']);
            case Type::FLICKR:
                return new Flickr($this->httpClient, $options['user_id']);
            case Type::INSTAGRAM:
                return new Instagram($this->httpClient, $options['token']);
            case Type::RSS:
                return new Rss($this->httpClient, $options['feed_url']);
            default:
                throw new RuntimeException('unsupported type: ' . (string) $type);
        }
    }
}
