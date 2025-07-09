<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Reservation;
use App\Models\Favorite;
use Carbon\Carbon;

class MyPageTest extends TestCase
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

    public function test_unauthenticated_user_cannot_access_mypage()
    {
        $response = $this->get('/mypage');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_mypage()
    {
        $response = $this->actingAs($this->user)
            ->get('/mypage');

        $response->assertStatus(200);
        $response->assertViewIs('mypage');
        $response->assertViewHas(['reservations', 'favorites']);
    }

    public function test_mypage_shows_future_reservations_only()
    {
    

        $pastReservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::yesterday()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);

        $futureReservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->get('/mypage');

        $reservations = $response->original->getData()['reservations'];

        $this->assertEquals(1, $reservations->count());
        $this->assertEquals($futureReservation->id, $reservations->first()->id);
    }

    public function test_mypage_shows_todays_future_reservations()
    {
        $pastTimeReservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::today()->format('Y-m-d'),
            'reservation_time' => Carbon::now()->subHour()->format('H:i:s'),
            'number_of_people' => 2
        ]);

        $futureTimeReservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::today()->format('Y-m-d'),
            'reservation_time' => Carbon::now()->addHour()->format('H:i:s'),
            'number_of_people' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->get('/mypage');

        $reservations = $response->original->getData()['reservations'];

        $this->assertEquals(1, $reservations->count());
        $this->assertEquals($futureTimeReservation->id, $reservations->first()->id);
    }

    public function test_mypage_shows_user_favorites()
    {
        $favorite = Favorite::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id
        ]);

        $response = $this->actingAs($this->user)
            ->get('/mypage');

        $favorites = $response->original->getData()['favorites'];

        $this->assertEquals(1, $favorites->count());
        $this->assertEquals($this->restaurant->id, $favorites->first()->restaurant_id);
    }

    public function test_user_can_delete_own_reservation()
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/reservations/' . $reservation->id . '/delete');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'deleted']);

        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation->id
        ]);
    }

    public function test_user_cannot_delete_other_users_reservation()
    {
        $timestamp = time();
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'otheruser' . $timestamp . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $reservation = Reservation::create([
            'user_id' => $otherUser->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/reservations/' . $reservation->id . '/delete');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'deleted']);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id
        ]);
    }

    public function test_reservation_time_is_formatted_correctly()
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:30:00',
            'number_of_people' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->get('/mypage');

        $reservations = $response->original->getData()['reservations'];

        $this->assertEquals('18:30', $reservations->first()->formatted_time);
    }

    public function test_reservations_include_restaurant_relationship()
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->get('/mypage');

        $reservations = $response->original->getData()['reservations'];

        $this->assertNotNull($reservations->first()->restaurant);
        $this->assertEquals($this->restaurant->name, $reservations->first()->restaurant->name);
    }

    public function test_favorites_include_restaurant_relationship()
    {
        $favorite = Favorite::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id
        ]);

        $response = $this->actingAs($this->user)
            ->get('/mypage');

        $favorites = $response->original->getData()['favorites'];

        $this->assertNotNull($favorites->first()->restaurant);
        $this->assertEquals($this->restaurant->name, $favorites->first()->restaurant->name);
    }
}
