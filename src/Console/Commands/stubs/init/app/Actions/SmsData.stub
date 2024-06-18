<?php

namespace App\Actions;

class SmsData
{

    /**
     * @param string $name
     * @param string $from
     * @param string $text
     * @param string $campaignType
     * @param null|string $schedule
     */
    public function __construct(public string $name,
        public string $text,
        public string $from = "APP_NAME",
        public string $campaignType = "transactional",
        public null|string $schedule = null
    ) { }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'from' => $this->from,
            'text' => $this->text,
            'campaign_type' => $this->campaignType,
            'schedule' => $this->schedule
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
