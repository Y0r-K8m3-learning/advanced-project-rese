<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RestaurantControllerTest extends TestCase
{

    use DatabaseTransactions;
    /**
     *@test
     */
    public function test_index(): void
    {
        //index
        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertViewIs('restaurant');
    }
}
