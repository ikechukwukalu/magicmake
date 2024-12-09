<?php

namespace App\Actions;

class FcmData
{

    /**
     * @param string $title
     * @param string $body
     * @param null|string $link
     * @param null|string $image
     * @param bool $external_link
     */
    public function __construct(public string $title,
        public string $body,
        public null|string $link = null,
        public null|string $image = null,
        public bool $external_link = false
    ) { }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'click_action' => $this->link,
            'image' => $this->image,
            'external_link' => $this->external_link
        ];
    }

}
