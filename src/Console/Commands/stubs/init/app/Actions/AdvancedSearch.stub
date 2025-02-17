<?php

namespace App\Actions;

use Illuminate\Support\Str;

class AdvancedSearch
{

    /**
     * Summary of __construct
     * @param string $relationship
     * @param string $foreignKeyString
     * @param string|array $column
     * @param string|null $parent
     */
    public function __construct(private string $relationship,
    private string $foreignKeyString, private string|array $column, private string|null $parent = null)
{ }

    /**
     * Summary of search
     * @param mixed $model
     * @param string $value
     * @return array|null
     */
    public function search(mixed $model, string $value): array|null|string|int
    {
        if (is_string($this->column)) {
            $records = $model->query()->where($this->column, 'LIKE', "%{$value}%")->get('id');
        } else {

            if (!$this->parent) {
                $query = $model->query();
                $index = 'id';

                foreach ($this->column as $key => $field)
                {
                    if ($key == 0) {
                        $query->where($field, 'LIKE', "%{$value}%");
                        continue;
                    }

                    $query->orWhere($field, 'LIKE', "%{$value}%");
                }

                $records = $query->get($index);

                if ($records?->count() > 0) {
                    return $records->pluck($index)->toArray();
                }
            } else {
                $model = json_decode(json_encode($model->toArray(), true), true);
                $model = collect( $model );

                $query = $model;
                $index = null;
                $records = null;

                foreach ($this->column as $key => $field)
                {
                    if ($records = $query->where($field, $value)->first()) {
                        $index = Str::snake($this->foreignKeyString);
                        break;
                    }
                }

                if ($records && array_key_exists($index, $records)) {
                    return $records[$index];
                }

                return null;
            }

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

    public function getParent(): string|null
    {
        return $this->parent;
    }
}
