<?php

declare(strict_types=1);

namespace App\Bcr;

use App\Bcr\SocialMediaService\Factory;
use App\Bcr\SocialMediaService\Type;
use Generator;
use function json_encode;
use function sha1;

class Configuration
{
    /** @var array<array<string,string|array<string,string>>> */
    private $configuration;

    /** @param array<array<string,string|array<string,string>>> $configuration */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getAllFeeds() : Generator
    {
        $factory = new Factory();
        foreach ($this->configuration as $configuration) {
            yield sha1(json_encode($configuration)) => $factory->create(new Type($configuration['type']), $configuration['options']);
        }
    }
}
