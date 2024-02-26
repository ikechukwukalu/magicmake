<?php

namespace Tests\Feature;

use App\Enums\ContactType;
use App\Models\ContactUs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactUsTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForCreateContactUs()
    {

        $postData = [
            'reason' => $this->faker->title,
            'subject' => $this->faker->title,
            'type' => ContactType::INQUIRY->value,
            'message' => '',
        ];

        $response = $this->postJson(route('createContactUs'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);
    }

    public function testCreateContactUs()
    {

        $postData = [
            'reason' => $this->faker->title,
            'subject' => $this->faker->title,
            'type' => ContactType::INQUIRY->value,
            'message' => $this->faker->text(maxNbChars: 150),
        ];

        $response = $this->postJson(route('createContactUs'), $postData);
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);

    }

    public function testErrorValidationForUpdateContactUs()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'id' => 'abc',
            'type' => ContactType::SUPPORT->value,
            'message' => $this->faker->text(maxNbChars: 150),
        ];

        $response = $this->putJson(route('updateContactUs'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);
    }

    public function testUpdateContactUs()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $contactUs = ContactUs::factory()->create();

        $postData = [
            'id' => $contactUs->id,
            'type' => ContactType::SUPPORT->value,
            'reason' => $this->faker->text(maxNbChars: 150),
            'message' => $this->faker->text(maxNbChars: 150),
        ];

        $response = $this->putJson(route('updateContactUs'), $postData);
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }

    public function testErrorValidationForDeleteContactUs()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'id' => 'abc'
        ];

        $response = $this->deleteJson(route('deleteContactUs'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);
    }

    public function testDeleteContactUs()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $contactUs = ContactUs::factory()->create();

        $postData = [
            'id' => $contactUs->id
        ];

        $response = $this->deleteJson(route('deleteContactUs'), $postData);
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }

    public function testReadContactUs(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $contactUs = ContactUs::factory()->create();

        $response = $this->getJson(route('readContactUs', ['id' => 'all']));
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);

        $response = $this->getJson(route('readContactUs', ['id' => $contactUs->id]));
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);

        $response = $this->getJson(route('readContactUs', ['id' => $contactUs->id]));
        $responseArray = $response->json();

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }
}
