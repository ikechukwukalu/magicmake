<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\User;

class TwoFactorTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateTwoFactor()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson(route('createTwoFactor'));
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
        $this->assertTrue( is_array($responseArray['data']) );
        $this->assertTrue( array_key_exists("qr_code", $responseArray['data']) );
        $this->assertTrue( array_key_exists("uri", $responseArray['data']) );
        $this->assertTrue( array_key_exists("string", $responseArray['data']) );
    }

    public function testErrorValidationForConfirmTwoFactor()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'code' => 'abcdef', //Wrong format
        ];

        $response = $this->postJson(route('confirmTwoFactor'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);
    }

    public function testDisableTwoFactor()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson(route('disableTwoFactor'));
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }

}
