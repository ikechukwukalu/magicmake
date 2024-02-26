<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\User;

class ForgotPasswordTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForForgotPassword()
    {
        $postData = [
            'email' => '0000000000.com', //Wrong email format
        ];

        $response = $this->postJson(route('forgotPassword'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);
    }

    public function testForgotPassword()
    {
        $postData = [
            'email' => $this->faker->unique()->safeEmail(), //Email doesn't exist
        ];

        $user = User::factory()->create([
                'email' => $postData['email']
        ]);

        $response = $this->postJson(route('forgotPassword'), $postData);
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }
}
