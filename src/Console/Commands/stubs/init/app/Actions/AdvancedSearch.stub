<?php

namespace App\Actions;

class AdvancedSearch
{

    /**
     * Summary of __construct
     * @param string $relationship
     * @param string $foreignKeyString
     * @param string|array $column
     */
    public function __construct(private string $relationship,
        private string $foreignKeyString, private string|array $column)
    { }

    /**
     * Summary of search
     * @param mixed $model
     * @param string $value
     * @return array|null
     */
    public function search(mixed $model, string $value): array|null
    {
        if (is_string($this->column)) {
            $records = $model->query()->where($this->column, 'LIKE', "%{$value}%")->get('id');
        } else {
            $query = $model->query();

            foreach ($this->column as $key => $field)
            {
                if ($key == 0) {
                    $query->where($field, 'LIKE', "%{$value}%");
                    continue;
                }

                $query->orWhere($field, 'LIKE', "%{$value}%");
            }

            $records = $query->get('id');
        }

        if ($records->count() > 0) {
            return $records->pluck('id')->toArray();
        }

        return null;
    }

    public function getForeignString(): string
    {
        return $this->foreignKeyString;
    }

    public function getRelationship(): string
    {
        return $this->relationship;
    }
}
