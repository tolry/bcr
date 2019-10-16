<?php

declare(strict_types=1);

namespace App\Bcr\SocialMediaService;

use App\Bcr\Feed\ListItem;

interface SocialMediaServiceInterface
{
    public function initializeApiRequest() : void;

    /**
     * @return ListItem[]
     */
    public function getList() : array;

    public function getHash() : string;

    public function getRefreshInterval() : int;
}
