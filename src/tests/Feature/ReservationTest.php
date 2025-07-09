<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::where('email', 'test_user1@example.com')->first();

        $this->area = Area::first();
        $this->genre = Genre::first();
        
        $timestamp = time();
        $this->restaurant = Restaurant::create([
            'name' => 'テスト寿司店' . $timestamp,
            'area_id' => $this->area->id,
            'genre_id' => $this->genre->id,
            'description' => 'テスト説明',
            'image_url' => 'test.jpg',
            'owner_id' => User::where('email', 'test_owner@example.com')->first()->id
        ]);
    }

    public function test_unauthenticated_user_cannot_make_reservation()
    {
        $response = $this->post('/reservations', [
            'restaurant_id' => $this->restaurant->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'time' => '18:00',
            'number' => 2
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_make_reservation()
    {
        $response = $this->actingAs($this->user)
                         ->post('/reservations', [
                             'restaurant_id' => $this->restaurant->id,
                             'date' => Carbon::tomorrow()->format('Y-m-d'),
                             'time' => '18:00',
                             'number' => 2
                         ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);
    }

    public function test_user_cannot_make_duplicate_reservation()
    {
        $date = Carbon::tomorrow()->format('Y-m-d');
        $time = '18:00';

        Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => $date,
            'reservation_time' => $time,
            'number_of_people' => 2
        ]);

        $response = $this->actingAs($this->user)
                         ->post('/reservations', [
                             'restaurant_id' => $this->restaurant->id,
                             'date' => $date,
                             'time' => $time,
                             'number' => 3
                         ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);
    }

    public function test_reservation_complete_page_can_be_rendered()
    {
        $response = $this->get('/reservation/complete');
        
        $response->assertStatus(200);
        $response->assertViewIs('reservation_complete');
    }



    public function test_user_can_update_reservation()
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);

        $newDate = Carbon::tomorrow()->addDay()->format('Y-m-d');

        $response = $this->actingAs($this->user)
                         ->put('/reservations/' . $reservation->id, [
                             'date' => $newDate,
                             'time' => '19:00',
                             'number_of_people' => 4
                         ]);

        $response->assertRedirect(route('mypage.index'));
        $response->assertSessionHas('status', '予約を変更しました。');
        
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'reservation_date' => $newDate,
            'reservation_time' => '19:00',
            'number_of_people' => 4
        ]);
    }

    public function test_user_can_view_qr_code_for_reservation()
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);

        $response = $this->actingAs($this->user)
                         ->get('/reservations/' . $reservation->id . '/qrcode');
        
        $response->assertStatus(200);
        $response->assertViewIs('qrcode');
        $response->assertViewHas('reservation');
        $response->assertViewHas('qrCode');
    }

    public function test_reservation_can_be_verified()
    {
        $reservation = Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2,
            'is_verified' => false
        ]);

        $response = $this->actingAs($this->user)
                         ->get('/reservations/verify/' . $reservation->id);
        
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'is_verified' => true
        ]);
    }

    public function test_stripe_payment_index_page_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->post('/payment/index', [
            'restaurant_id' => $this->restaurant->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'time' => '18:00',
            'number' => 2
        ]);
        $response->assertViewIs('paymentindex');
        $response->assertViewHas(['number', 'time', 'date', 'restaurant_id']);
    }

    public function test_unauthenticated_user_cannot_access_payment()
    {
        $response = $this->post('/payment', [
            'restaurant_id' => $this->restaurant->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'time' => '18:00',
            'number' => 2,
            'total_price' => 5000,
            'stripeToken' => 'tok_visa'
        ]);

        $response->assertRedirect('/login');
    }

    public function test_payment_complete_page_can_be_rendered()
    {
        $response = $this->get('/complete');
        
        $response->assertStatus(200);
        $response->assertViewIs('reservation_complete');
    }
}