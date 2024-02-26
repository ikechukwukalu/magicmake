<?php

namespace Tests\Feature;

use App\Models\UserDeviceToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserDeviceTokenTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForCreateUserDeviceToken()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'device_token' => null,
        ];

        $response = $this->postJson(route('createUserDeviceToken'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);
    }

    public function testCreateUserDeviceToken()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'device_token' => $this->faker->name(),
        ];

        $response = $this->postJson(route('createUserDeviceToken'), $postData);
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }

    public function testErrorValidationForUpdateUserDeviceToken()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'id' => 'abc',
            'active' => 1,
        ];

        $response = $this->putJson(route('updateUserDeviceToken'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);
    }

    public function testUpdateUserDeviceToken()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $userDeviceToken = UserDeviceToken::factory()->create([
            'user_id' => $user->id
        ]);

        $postData = [
            'id' => $userDeviceToken->id,
            'active' => 1,
        ];

        $response = $this->putJson(route('updateUserDeviceToken'), $postData);
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }

    public function testErrorValidationForDeleteUserDeviceToken()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'id' => 'abc'
        ];

        $response = $this->deleteJson(route('deleteUserDeviceToken'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);
    }

    public function testDeleteUserDeviceToken()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $userDeviceToken = UserDeviceToken::factory()->create([
            'user_id' => $user->id
        ]);

        $postData = [
            'id' => $userDeviceToken->id
        ];

        $response = $this->deleteJson(route('deleteUserDeviceToken'), $postData);
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }

    public function testReadUserDeviceToken(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $userDeviceToken = UserDeviceToken::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJson(route('readUserDeviceToken', ['id' => 'all']));
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);

        $response = $this->getJson(route('readUserDeviceToken', ['id' => $userDeviceToken->id]));
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);

        $response = $this->getJson(route('readUserDeviceToken'));
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }

}
