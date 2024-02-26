<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Country extends Model
{
    use Sushi, HasFactory;

    public function getRows()
    {
        $countries = config('countries');

        foreach ($countries as $key => $country) {
            $countries[$key]['states'] = is_null($country['states']) ?
                                            json_encode([]) :
                                            json_encode($country['states']);
        }

        return $countries;
    }

    protected function states(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => collect(json_decode($value, true))
        );
    }

    protected function sushiShouldCache()
    {
        return false;
    }
}
