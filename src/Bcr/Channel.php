<?php

declare(strict_types=1);

namespace App\Bcr;

use function sprintf;

class Channel
{
    public string $id;
    public array $icon;
    public string $label;
    public string $type;

    private function __construct(
        string $icon,
        string $label,
        ?string $type = null,
        string $iconPrefix = 'fab'
    ) {
        $this->icon  = [$iconPrefix, $icon];
        $this->label = $label;
        $this->type  = $type ?? $icon;

        $this->id = sprintf('%s :: %s', $icon, $label);
    }

    public static function youtube(string $label) : self
    {
        return new self('youtube', $label);
    }

    public static function flickr(string $label) : self
    {
        return new self('flickr', $label);
    }

    public static function twitter(string $label) : self
    {
        return new self('twitter', $label);
    }

    public static function instagram(string $label) : self
    {
        return new self('instagram', $label);
    }

    public static function rss(string $label) : self
    {
        return new self('rss', $label, null, 'fas');
    }
}
