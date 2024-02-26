<?php

namespace App\Actions;

class ResponseData
{

    /**
     * @param bool $success
     * @param int $status_code
     * @param string $message
     * @param null|object|array $data
     */
    public function __construct(public bool $success,
        public int $status_code,
        public string $message,
        public null|object|array $data = []
    ) { }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'status_code' => $this->status_code,
            'message' => $this->message,
            'data' => $this->data
        ];
    }

    /**
     * Convert array to json.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

}
