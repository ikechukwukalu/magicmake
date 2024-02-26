<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class EditProfileTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForEditProfile()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'name' => 'Test User 2',
            'email' => 'testuser2gmail.com', //Wrong email format
        ];

        $response = $this->postJson(route('editProfile'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);

        //This test would also run correctly if an existing email is passed
    }

    public function testEditProfile()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->email(),
        ];

        $response = $this->postJson(route('editProfile'), $postData);
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }
}
