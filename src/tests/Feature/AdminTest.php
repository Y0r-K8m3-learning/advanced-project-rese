<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::where('email', 'test_admin@example.com')->first();
        $this->user = User::where('email', 'test_user1@example.com')->first();
        $this->owner = User::where('email', 'test_owner@example.com')->first();
    }

    public function test_non_admin_user_cannot_access_admin_pages()
    {
        $response = $this->actingAs($this->user)
                         ->get('/admin/owners');

        $response->assertStatus(403);
    }

    public function test_owner_cannot_access_admin_pages()
    {
        $response = $this->actingAs($this->owner)
                         ->get('/admin/owners');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_owners_index()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/owners');

        $response->assertStatus(200);
        $response->assertViewIs('admin.owners.index');
        $response->assertViewHas(['owners', 'users']);
    }

    public function test_admin_can_create_owner()
    {
        $timestamp = time();
        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners', [
                             'name' => 'New Owner ' . $timestamp,
                             'email' => 'newowner' . $timestamp . '@example.com',
                             'password' => 'password123'
                         ]);

        $response->assertRedirect(route('admin.owners.index'));
        $response->assertSessionHas('success', 'オーナーが登録されました');
        
        $this->assertDatabaseHas('users', [
            'name' => 'New Owner ' . $timestamp,
            'email' => 'newowner' . $timestamp . '@example.com',
            'role_id' => User::ROLE_OWNER
        ]);
    }

    public function test_admin_cannot_create_owner_with_duplicate_email()
    {
        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners', [
                             'name' => 'Duplicate Owner',
                             'email' => 'test_owner@example.com',
                             'password' => 'password123'
                         ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_upload_valid_csv_file()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,東京都,寿司,美味しい寿司店です,test.jpg\n";
        $csvContent .= "テスト焼肉店,大阪府,焼肉,絶品焼肉店です,yakiniku.png";
        
        $csvFile = UploadedFile::fake()->createWithContent('restaurants.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('import_complete', 'CSVファイルの店舗情報が登録されました');
        
        $this->assertDatabaseHas('restaurants', [
            'name' => 'テスト店舗',
            'description' => '美味しい寿司店です'
        ]);
        
        $this->assertDatabaseHas('restaurants', [
            'name' => 'テスト焼肉店',
            'description' => '絶品焼肉店です'
        ]);
    }

    public function test_admin_cannot_upload_csv_with_invalid_header()
    {
        Storage::fake('local');
        
        $csvContent = "名前,場所,種類,説明,写真\n";
        $csvContent .= "テスト店舗,東京都,寿司,美味しい寿司店です,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('invalid.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'CSVファイルのヘッダー情報が正しくありません。');
    }

    public function test_admin_cannot_upload_csv_with_invalid_area()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,北海道,寿司,美味しい寿司店です,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('invalid_area.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors();
    }

    public function test_admin_cannot_upload_csv_with_invalid_genre()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,東京都,中華,美味しい寿司店です,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('invalid_genre.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors();
    }

    public function test_admin_cannot_upload_csv_with_invalid_image_extension()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,東京都,寿司,美味しい寿司店です,test.gif";
        
        $csvFile = UploadedFile::fake()->createWithContent('invalid_image.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors();
    }

    public function test_csv_import_assigns_random_owner()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,東京都,寿司,美味しい寿司店です,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('restaurants.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $restaurant = Restaurant::where('name', 'テスト店舗')->first();
        $this->assertNotNull($restaurant);
        $this->assertEquals(User::ROLE_OWNER, User::find($restaurant->owner_id)->role_id);
    }

    public function test_owners_index_displays_users_and_owners()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/owners');

        $owners = $response->original->getData()['owners'];
        $users = $response->original->getData()['users'];
        
        $this->assertGreaterThan(0, $owners->count());
        $this->assertGreaterThan(0, $users->count());
        $this->assertEquals(User::ROLE_OWNER, $owners->first()->role_id);
        $this->assertEquals(User::ROLE_USER, $users->first()->role_id);
    }
}