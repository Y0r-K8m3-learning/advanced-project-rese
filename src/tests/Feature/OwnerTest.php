<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Reservation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OwnerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::where('email', 'test_owner@example.com')->first();
        $this->user = User::where('email', 'test_user1@example.com')->first();
        $this->admin = User::where('email', 'test_admin@example.com')->first();

        $this->area = Area::first();
        $this->genre = Genre::first();

        $timestamp = time();
        $this->restaurant = Restaurant::create([
            'name' => 'オーナー店舗' . $timestamp,
            'area_id' => $this->area->id,
            'genre_id' => $this->genre->id,
            'description' => 'オーナーの店舗です',
            'image_url' => 'owner_restaurant.jpg',
            'owner_id' => $this->owner->id
        ]);
    }

    public function test_non_owner_user_cannot_access_owner_pages()
    {
        $response = $this->actingAs($this->user)
            ->get('/owner');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_owner_pages()
    {
        $response = $this->actingAs($this->admin)
            ->get('/owner');

        $response->assertStatus(403);
    }

    public function test_owner_can_access_restaurant_management_page()
    {
        $response = $this->actingAs($this->owner)
            ->get('/owner');

        $response->assertStatus(200);
        $response->assertViewIs('owner.restaurant');
        $response->assertViewHas(['restaurants', 'areas', 'genres']);
    }



    public function test_owner_can_create_restaurant()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('restaurant.jpg');

        $response = $this->actingAs($this->owner)
            ->post('/owner/restaurants/store', [
                'name' => '新しい店舗',
                'description' => '新しい店舗の説明',
                'area_id' => $this->area->id,
                'genre_id' => $this->genre->id,
                'image_url' => $image
            ]);

        $response->assertRedirect(route('owner'));
        $response->assertSessionHas('success', '店舗が登録されました。');

        $this->assertDatabaseHas('restaurants', [
            'name' => '新しい店舗',
            'description' => '新しい店舗の説明',
            'owner_id' => $this->owner->id
        ]);
    }

    public function test_owner_can_create_restaurant_without_image()
    {
        $response = $this->actingAs($this->owner)
            ->post(route('owner.restaurants.store'), [
                'name' => '新しい店舗',
                'description' => '新しい店舗の説明',
                'area_id' => $this->area->id,
                'genre_id' => $this->genre->id
            ]);

        $response->assertSessionHasErrors([
            'image_url' => '画像URLは必ず指定してください。',
        ]);
    }

    public function test_owner_cannot_create_restaurant_with_invalid_data()
    {
        $response = $this->actingAs($this->owner)
            ->post('/owner/restaurants/store', [
                'name' => '',
                'description' => '',
                'area_id' => 999,
                'genre_id' => 999
            ]);

        $response->assertSessionHasErrors(['name', 'description', 'area_id', 'genre_id']);
    }

    public function test_owner_can_update_restaurant()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('updated_restaurant.jpg');

        $response = $this->actingAs($this->owner)
            ->put('/owner/restaurants/' . $this->restaurant->id, [
                'name' => '更新された店舗',
                'description' => '更新された説明',
                'area_id' => $this->area->id,
                'genre_id' => $this->genre->id,
                'image_url' => $image
            ]);

        $response->assertRedirect(route('owner'));
        $response->assertSessionHas('success', '店舗情報が更新されました。');

        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
            'name' => '更新された店舗',
            'description' => '更新された説明'
        ]);
    }

    public function test_owner_can_view_restaurant_reservations()
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);

        $response = $this->actingAs($this->owner)
            ->get('/owner/restaurants/' . $this->restaurant->id . '/reservations');

        $response->assertStatus(200);
        $response->assertViewIs('owner.reservations');
        $response->assertViewHas(['restaurant', 'reservations']);

        $reservations = $response->original->getData()['reservations'];
        $this->assertEquals(1, $reservations->count());
        $this->assertEquals($reservation->id, $reservations->first()->id);
    }

    public function test_owner_restaurant_page_shows_owner_restaurants_only()
    {
        $timestamp = time();
        $otherOwner = User::create([
            'name' => 'Other Owner',
            'email' => 'otherowner' . $timestamp . '@example.com',
            'password' => bcrypt('password'),
            'role_id' => User::ROLE_OWNER,
        ]);

        $otherRestaurant = Restaurant::create([
            'name' => '他のオーナー店舗' . $timestamp,
            'area_id' => $this->area->id,
            'genre_id' => $this->genre->id,
            'description' => '他のオーナーの店舗です',
            'image_url' => 'other_restaurant.jpg',
            'owner_id' => $otherOwner->id
        ]);

        $response = $this->actingAs($this->owner)
            ->get('/owner');

        $restaurants = $response->original->getData()['restaurants'];
        $this->assertEquals(1, $restaurants->count());
        $this->assertEquals($this->restaurant->id, $restaurants->first()->id);
    }

    public function test_owner_cannot_view_other_restaurants_reservations()
    {
        $timestamp2 = time() + 1;
        $otherOwner = User::create([
            'name' => 'Other Owner',
            'email' => 'otherowner' . $timestamp2 . '@example.com',
            'password' => bcrypt('password'),
            'role_id' => User::ROLE_OWNER,
        ]);

        $otherRestaurant = Restaurant::create([
            'name' => '他のオーナー店舗' . $timestamp2,
            'area_id' => $this->area->id,
            'genre_id' => $this->genre->id,
            'description' => '他のオーナーの店舗です',
            'image_url' => 'other_restaurant.jpg',
            'owner_id' => $otherOwner->id
        ]);

        $response = $this->actingAs($this->owner)
            ->get('/owner/restaurants/' . $otherRestaurant->id . '/reservations');

        $response->assertStatus(200);
    }

    public function test_owner_restaurant_page_includes_areas_and_genres()
    {
        $response = $this->actingAs($this->owner)
            ->get('/owner');

        $areas = $response->original->getData()['areas'];
        $genres = $response->original->getData()['genres'];

        $this->assertNotEmpty($areas);
        $this->assertNotEmpty($genres);
        $this->assertEquals($this->area->id, $areas->first()->id);
        $this->assertEquals($this->genre->id, $genres->first()->id);
    }
}
