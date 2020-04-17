<?php

declare(strict_types=1);

namespace App\Bcr;

use App\Bcr\SocialMediaService\Factory;
use App\Bcr\SocialMediaService\Type;
use Generator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Configuration
{
    private array $configuration;

    /** @param array<array<string,string|array<string,string>>> $configuration */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getAllFeeds(HttpClientInterface $httpClient) : Generator
    {
        $factory = new Factory($httpClient);
        foreach ($this->configuration as $configuration) {
            yield $factory->create(new Type($configuration['type']), $configuration['options']);
        }
    }
}
