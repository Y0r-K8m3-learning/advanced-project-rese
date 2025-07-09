<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Favorite;

class FavoriteTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('email', 'test_user1@example.com')->first();

        $this->area = Area::first();
        $this->genre = Genre::first();

        $timestamp = time();
        $owner = User::where('email', 'test_owner@example.com')->first();
        $this->restaurant = Restaurant::create([
            'name' => 'テスト寿司店' . $timestamp,
            'area_id' => $this->area->id,
            'genre_id' => $this->genre->id,
            'description' => 'テスト説明',
            'image_url' => 'test.jpg',
            'owner_id' => $owner->id
        ]);
    }

    public function test_unauthenticated_user_cannot_add_favorite()
    {
        $response = $this->postJson('/restaurants/' . $this->restaurant->id . '/favorite');

        $response->assertStatus(401); 
        $response->assertJsonFragment([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_authenticated_user_can_add_favorite()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/restaurants/' . $this->restaurant->id . '/favorite');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'added']);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id
        ]);
    }

    public function test_user_cannot_add_duplicate_favorite()
    {
        Favorite::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/restaurants/' . $this->restaurant->id . '/favorite');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'added']);

        $this->assertEquals(1, Favorite::where('user_id', $this->user->id)
            ->where('restaurant_id', $this->restaurant->id)
            ->count());
    }

    public function test_authenticated_user_can_remove_favorite()
    {
        Favorite::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/restaurants/' . $this->restaurant->id . '/unfavorite');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'removed']);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id
        ]);
    }

    public function test_user_can_remove_nonexistent_favorite()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/restaurants/' . $this->restaurant->id . '/unfavorite');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'removed']);
    }

    public function test_favorite_restaurant_ids_are_included_in_restaurant_index()
    {
        Favorite::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id
        ]);

        $response = $this->actingAs($this->user)
            ->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('favoriteRestaurantIds');

        $favoriteIds = $response->original->getData()['favoriteRestaurantIds'];
        $this->assertContains($this->restaurant->id, $favoriteIds);
    }

    public function test_unauthenticated_user_has_empty_favorite_list()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('favoriteRestaurantIds');

        $favoriteIds = $response->original->getData()['favoriteRestaurantIds'];
        $this->assertEquals([], $favoriteIds);
    }

    public function test_favorite_post_route_works()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/restaurants/' . $this->restaurant->id . '/favorite');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'added']);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id
        ]);
    }

    public function test_user_favorites_are_accessible_through_relationship()
    {
        Favorite::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id
        ]);

        $user = User::with('favorites')->find($this->user->id);

        $this->assertEquals(1, $user->favorites->count());
        $this->assertEquals($this->restaurant->id, $user->favorites->first()->restaurant_id);
    }
}
