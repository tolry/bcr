<?php

namespace App\Bcr;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class Channel
{
    public $id;
    public $icon;
    public $label;

    private function __construct(string $icon, string $label, bool $video = false, string $videoType = null)
    {
        $this->icon = $icon;
        $this->label = $label;

        $this->id = sprintf('%s :: %s', $icon, $label);
    }

    public static function youtube($label): self
    {
        return new self('youtube', $label);
    }

    public static function flickr($label): self
    {
        return new self('flickr', $label);
    }

    public static function twitter($label): self
    {
        return new self('twitter', $label);
    }

    public static function instagram($label): self
    {
        return new self('instagram', $label);
    }
}
