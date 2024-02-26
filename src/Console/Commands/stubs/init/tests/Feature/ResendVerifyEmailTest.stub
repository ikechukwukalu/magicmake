<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class ResendVerifyEmailTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResendVerifyEmail()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('verification.resend'));
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }
}
