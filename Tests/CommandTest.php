<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

class CommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_fires_make_service_command(): void
    {
        $this->artisan('magic:init')->assertSuccessful();

        $this->artisan('magic:model DeliveryType')->assertSuccessful();

        $this->artisan('magic:contract DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:repository DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:service DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:controller DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:createRequest DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:updateRequest DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:deleteRequest DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:readRequest DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:api DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:test DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();

        $this->artisan('magic:factory DeliveryType --variable=deliveryType --underscore=delivery_type')->assertSuccessful();
    }
}
