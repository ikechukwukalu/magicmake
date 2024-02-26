<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class State extends Model
{
    use Sushi, HasFactory;


    public function getRows()
    {
        $countries = config('countries');
        $states = [];

        foreach ($countries as $country) {
            $countryStates = $country['states'];

            foreach ($countryStates as $key => $state) {
                $countryStates[$key]['country'] = $country['name'];
                $countryStates[$key]['country_code'] = $country['iso3'];
            }

            array_push($states, ...$countryStates);
        }

        return $states;
    }


}
