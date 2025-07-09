<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;
use App\Models\User;
use App\Models\Review;
use App\Models\Favorite;

class RestaurantSearchTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::first();
        $this->genre = Genre::first();

        $timestamp = time();
        $this->restaurant1 = Restaurant::create([
            'name' => 'テスト寿司店' . $timestamp,
            'area_id' => $this->area->id,
            'genre_id' => $this->genre->id,
            'description' => 'テスト説明',
            'image_url' => 'test.jpg',
            'owner_id' => User::where('email', 'test_owner@example.com')->first()->id
        ]);

        $this->restaurant2 = Restaurant::create([
            'name' => '別のレストラン' . $timestamp,
            'area_id' => $this->area->id,
            'genre_id' => $this->genre->id,
            'description' => 'テスト説明2',
            'image_url' => 'test2.jpg',
            'owner_id' => User::where('email', 'test_owner@example.com')->first()->id
        ]);
    }

    public function test_restaurant_index_page_can_be_rendered()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('restaurant');
    }

    public function test_restaurants_can_be_filtered_by_area()
    {
        $response = $this->get('/?area=' . $this->area->id);

        $response->assertStatus(200);
        $response->assertViewHas('restaurants');
    }

    public function test_restaurants_can_be_filtered_by_genre()
    {
        $response = $this->get('/?genre=' . $this->genre->id);

        $response->assertStatus(200);
        $response->assertViewHas('restaurants');
    }

    public function test_restaurants_can_be_searched_by_name()
    {
        $response = $this->get('/?name=テスト');

        $response->assertStatus(200);
        $response->assertViewHas('restaurants');
    }

    public function test_restaurants_can_be_sorted_randomly()
    {
        $response = $this->get('/?sort=random');

        $response->assertStatus(200);
        $response->assertViewHas('restaurants');
    }

    public function test_restaurants_can_be_sorted_by_rating_desc()
    {
        $timestamp = time();
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser' . $timestamp . '@example.com',
            'password' => bcrypt('password'),
            'role_id' => User::ROLE_USER,
            'email_verified_at' => now(),
        ]);

        $user->markEmailAsVerified();

        Review::create([
            'user_id' => $user->id,
            'restaurant_id' => $this->restaurant1->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        Review::create([
            'user_id' => $user->id,
            'restaurant_id' => $this->restaurant2->id,
            'rating' => 3,
            'comment' => 'Good restaurant'
        ]);

        $response = $this->get('/?sort=asc');

        $response->assertStatus(200);
        $response->assertViewHas('restaurants');
    }

    public function test_restaurants_can_be_sorted_by_rating_asc()
    {
        $timestamp = time() + 1;
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser' . $timestamp . '@example.com',
            'password' => bcrypt('password'),
            'role_id' => User::ROLE_USER,
            'email_verified_at' => now(),
        ]);

        $user->markEmailAsVerified();

        Review::create([
            'user_id' => $user->id,
            'restaurant_id' => $this->restaurant1->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        $response = $this->get('/?sort=desc');

        $response->assertStatus(200);
        $response->assertViewHas('restaurants');
    }

    public function test_restaurant_detail_page_can_be_rendered()
    {
        $response = $this->get('/restaurant/' . $this->restaurant1->id);

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_add_favorite()
    {
        $timestamp = time();
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser' . $timestamp . '@example.com',
            'password' => bcrypt('password'),
            'role_id' => User::ROLE_USER,
            'email_verified_at' => now(),
        ]);

        $user->markEmailAsVerified();

        $response = $this
            ->actingAs($user)
            ->postJson("/restaurants/{$this->restaurant1->id}/favorite");
        $response->assertStatus(200)
            ->assertJson(['status' => 'added']);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'restaurant_id' => $this->restaurant1->id
        ]);
    }

    public function test_authenticated_user_can_remove_favorite()
    {
        $timestamp = time() + 3;
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser' . $timestamp . '@example.com',
            'password' => bcrypt('password'),
            'role_id' => User::ROLE_USER,
            'email_verified_at' => now(),
        ]);

        $user->markEmailAsVerified();

        Favorite::create([
            'user_id' => $user->id,
            'restaurant_id' => $this->restaurant1->id
        ]);

        $response = $this->actingAs($user)
            ->postJson('/restaurants/' . $this->restaurant1->id . '/unfavorite');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'removed']);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'restaurant_id' => $this->restaurant1->id
        ]);
    }

    public function test_unauthenticated_user_cannot_add_favorite()
    {
        $response = $this->postJson('/restaurants/' . $this->restaurant1->id . '/favorite');

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
