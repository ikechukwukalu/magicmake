<?php

namespace App\Actions;

class UserNotificationData
{

    /**
     * @param string|int $user_id
     * @param string $title
     * @param string $body
     * @param null|string $link
     * @param null|string $image
     * @param bool $external_link
     */
    public function __construct(public string|int $user_id,
        public string $title,
        public string $body,
        public null|string $link = null,
        public null|string $image = null,
        public bool $external_link = false
    ) { }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'title' => $this->title,
            'body' => $this->body,
            'link' => $this->link,
            'image' => $this->image,
            'external_link' => $this->external_link
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toObject(): object
    {
        return (object) $this->toArray();
    }
}
