<?php

namespace Database\Factories;

use App\Enums\ContactType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactUs>
 */
class ContactUsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reason' => $this->faker->title,
            'subject' => $this->faker->title,
            'type' => ContactType::INQUIRY->value,
            'message' => $this->faker->text(maxNbChars: 150),
        ];
    }
}
