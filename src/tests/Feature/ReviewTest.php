<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\Reservation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReviewTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('email', 'test_user1@example.com')->first();
        $this->admin = User::where('email', 'test_admin@example.com')->first();

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

        Reservation::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::yesterday()->format('Y-m-d'),
            'reservation_time' => '18:00',
            'number_of_people' => 2
        ]);
    }

    public function test_review_show_returns_reviews_json()
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        $response = $this->get('/restaurants/review/show/' . $this->restaurant->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_id',
                'restaurant_id',
                'rating',
                'comment',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_authenticated_user_can_view_review_create_page()
    {
        $response = $this->actingAs($this->user)
            ->get('/restaurants/review/' . $this->restaurant->id);

        $response->assertStatus(200);
        $response->assertViewIs('review.create');
        $response->assertViewHas('restaurant');
    }

    public function test_unauthenticated_user_cannot_create_review()
    {
        $response = $this->post('/restaurants/review', [
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        $response->assertRedirect('/login');
    }

    public function test_user_can_create_review_with_valid_data()
    {
        $response = $this->actingAs($this->user)
            ->post('/restaurants/review', [
                'restaurant_id' => $this->restaurant->id,
                'rating' => 5,
                'comment' => 'Great restaurant'
            ]);

        $response->assertRedirect(route('review.complete', ['restaurant_id' => $this->restaurant->id]));
        $response->assertSessionHas('complete', '口コミを投稿しました');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);
    }

    public function test_user_cannot_create_duplicate_review()
    {
        Review::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'First review'
        ]);

        $response = $this->actingAs($this->user)
            ->post('/restaurants/review', [
                'restaurant_id' => $this->restaurant->id,
                'rating' => 4,
                'comment' => 'Second review'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'この店舗の口コミは既に投稿済みです');
    }

    public function test_user_can_create_review_with_image()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('review.jpeg');

        $response = $this->actingAs($this->user)
            ->post('/restaurants/review', [
                'restaurant_id' => $this->restaurant->id,
                'rating' => 5,
                'comment' => 'Great restaurant',
                'image' => $image
            ]);

        $response->assertRedirect(route('review.complete', ['restaurant_id' => $this->restaurant->id]));

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
        ]);

        $this->assertDatabaseHas('review_images', [
            'review_id' => Review::where(['user_id'=> $this->user->id, 'restaurant_id' => $this->restaurant->id])->first()->id
        ]);
    }

    public function test_user_can_view_edit_page_for_own_review()
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        $response = $this->actingAs($this->user)
            ->get('/restaurants/review/edit/' . $this->restaurant->id . '/' . $review->id);

        $response->assertStatus(200);
        $response->assertViewIs('review.edit');
        $response->assertViewHas('user_review');
    }

    public function test_user_can_update_own_review()
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        $response = $this->actingAs($this->user)
            ->patch('/restaurants/review/update', [
                'restaurant_id' => $this->restaurant->id,
                'rating' => 4,
                'comment' => 'Updated review'
            ]);

        $response->assertRedirect(route('review.complete', ['restaurant_id' => $this->restaurant->id]));
        $response->assertSessionHas('complete', '口コミを更新しました');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => 'Updated review'
        ]);
    }

    public function test_user_can_view_delete_page_for_own_review()
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        $response = $this->actingAs($this->user)
            ->get('/restaurants/review/delete/' . $this->restaurant->id . '/' . $review->id);

        $response->assertStatus(200);
        $response->assertViewIs('review.delete');
    }

    public function test_user_can_delete_own_review()
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        $response = $this->actingAs($this->user)
            ->delete('/restaurants/review/destroy', [
                'review_id' => $review->id,
                'restaurant_id' => $this->restaurant->id
            ]);

        $response->assertRedirect(route('review.complete', ['restaurant_id' => $this->restaurant->id]));
        $response->assertSessionHas('complete', '口コミを削除しました');

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id
        ]);
    }

    public function test_admin_can_delete_any_review()
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        $response = $this->actingAs($this->admin)
            ->delete('/restaurants/review/destroy', [
                'review_id' => $review->id,
                'restaurant_id' => $this->restaurant->id
            ]);

        $response->assertRedirect(route('review.complete', ['restaurant_id' => $this->restaurant->id]));

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id
        ]);
    }

    public function test_user_cannot_delete_other_users_review()
    {
        $timestamp = time();
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'otheruser' . $timestamp . '@example.com',
            'password' => bcrypt('password'),
            'role_id' => User::ROLE_USER,
        ]);

        $review = Review::create([
            'user_id' => $otherUser->id,
            'restaurant_id' => $this->restaurant->id,
            'rating' => 5,
            'comment' => 'Great restaurant'
        ]);

        $response = $this->actingAs($this->user)
            ->delete('/restaurants/review/destroy', [
                'review_id' => $review->id,
                'restaurant_id' => $this->restaurant->id
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'この口コミは削除できません');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id
        ]);
    }

    public function test_review_complete_page_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get('/restaurants/review/complete/' . $this->restaurant->id);

        $response->assertStatus(200);
        $response->assertViewIs('review.complete');
        $response->assertViewHas('restaurant_id');
    }
}
