<?php

namespace Tests\Feature;

use App\Events\EmailVerification;
use App\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;


class RegistrationTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForRegistration()
    {
        $postData = [
            'name' => $this->faker->name(),
            'email' => '', //Wrong email format
            'password' => 'password',
            'password_confirmation' => '1234567' //None matching passwords
        ];

        $response = $this->postJson(route('register'), $postData);
        $responseArray = $response->json();

        $this->assertFalse($responseArray['success']);

        //This test would also run correctly if an existing email is passed
    }

    public function testRegistration()
    {

        $notification = Notification::fake();
        $event = Event::fake();

        $postData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->email(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'password' => "{_'hhtl[N#%H3BXe",
            'password_confirmation' => "{_'hhtl[N#%H3BXe"
        ];

        $response = $this->postJson(route('register'), $postData);
        $responseArray = $response->json();

        // Assert that a given number of notifications were sent...
        $notification->assertCount(1);
        $event->assertDispatched(EmailVerification::class);
        $event->assertListening(EmailVerification::class, SendEmailVerificationNotification::class);

        $response->assertOk();
        $this->assertTrue($responseArray['success']);
    }
}
