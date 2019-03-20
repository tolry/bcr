<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use MyCLabs\Enum\Enum;

class Type extends Enum
{
    public const YOUTUBE   = 'youtube';
    public const TWITTER   = 'twitter';
    public const INSTAGRAM = 'instagram';
    public const FLICKR    = 'flickr';
    public const RSS       = 'rss';
}
