<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForLogin()
    {
        $postData = [
            'email' => $this->faker->unique()->safeEmail(), //Wrong email format
            'password' => '' //Empty passwords
        ];

        $response = $this->postJson(route('login'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);
    }

    public function testLoginThrottling()
    {

        $postData = [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make("{_'hhtl[N#%H3BXe")
        ];

        $user = User::factory()->create([
            'email' => $postData['email']
        ]);

        //Multipe login attempts
        for($i = 1; $i <= 6; $i ++) {
            $response = $this->postJson(route('login'), $postData);
        }

        $responseArray = $response->json();

        $this->assertEquals(400, $responseArray['status_code']);
        $this->assertFalse($responseArray['success']);
    }

    public function testLogin()
    {

        $notification = Notification::fake();

        $postData = [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make("{_'hhtl[N#%H3BXe")
        ];

        $user = User::factory()->create([
            'email' => $postData['email'],
            'password' => Hash::make($postData['password'])
        ]);

        $response = $this->postJson(route('login'), $postData);
        $responseArray = $response->json();

        // Assert that a given number of notifications were sent...
        $notification->assertCount(1);

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }
}
