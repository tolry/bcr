<?php

namespace App\Bcr;

use App\Bcr\SocialMediaService\Factory;
use App\Bcr\SocialMediaService\Type;

class Configuration
{
    private $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getAllFeeds(): \Generator
    {
        $factory = new Factory();
        foreach ($this->configuration as $configuration) {
            yield sha1(json_encode($configuration)) => $factory->create(new Type($configuration['type']), $configuration['options']);
        }
    }
}
