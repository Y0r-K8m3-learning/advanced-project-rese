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

class CsvImportTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::where('email', 'test_admin@example.com')->first();
        $this->owner = User::where('email', 'test_owner@example.com')->first();
    }

    public function test_admin_can_import_valid_csv_file()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "仙台寿司店,東京都,寿司,新鮮な寿司をお楽しみください,sushi.jpg\n";
        $csvContent .= "大阪焼肉店,大阪府,焼肉,最高級の焼肉をご提供,yakiniku.png\n";
        $csvContent .= "福岡イタリアン,福岡県,イタリアン,本格的なイタリア料理,italian.jpeg";
        
        $csvFile = UploadedFile::fake()->createWithContent('restaurants.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('import_complete', 'CSVファイルの店舗情報が登録されました');
        
        $this->assertDatabaseHas('restaurants', [
            'name' => '仙台寿司店',
            'description' => '新鮮な寿司をお楽しみください',
            'image_url' => 'sushi.jpg'
        ]);
        
        $this->assertDatabaseHas('restaurants', [
            'name' => '大阪焼肉店',
            'description' => '最高級の焼肉をご提供',
            'image_url' => 'yakiniku.png'
        ]);
        
        $this->assertDatabaseHas('restaurants', [
            'name' => '福岡イタリアン',
            'description' => '本格的なイタリア料理',
            'image_url' => 'italian.jpeg'
        ]);
    }

    public function test_csv_import_fails_with_wrong_header()
    {
        Storage::fake('local');
        
        $csvContent = "名前,エリア,カテゴリ,詳細,写真\n";
        $csvContent .= "テスト店舗,東京都,寿司,説明,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('wrong_header.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'CSVファイルのヘッダー情報が正しくありません。');
    }

    public function test_csv_import_fails_with_missing_columns()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル\n";
        $csvContent .= "テスト店舗,東京都,寿司";
        
        $csvFile = UploadedFile::fake()->createWithContent('missing_columns.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'CSVファイルのヘッダー情報が正しくありません。');
    }

    public function test_csv_import_fails_with_invalid_area()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,北海道,寿司,美味しい寿司店,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('invalid_area.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors();
    }

    public function test_csv_import_fails_with_invalid_genre()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,東京都,中華,美味しい中華料理店,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('invalid_genre.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors();
    }

    public function test_csv_import_fails_with_invalid_image_extension()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,東京都,寿司,美味しい寿司店,test.gif";
        
        $csvFile = UploadedFile::fake()->createWithContent('invalid_image.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors();
    }

    public function test_csv_import_fails_with_empty_required_fields()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= ",東京都,寿司,美味しい寿司店,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('empty_name.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors();
    }

    public function test_csv_import_fails_with_too_long_name()
    {
        Storage::fake('local');
        
        $longName = str_repeat('あ', 51);
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "{$longName},東京都,寿司,美味しい寿司店,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('long_name.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors();
    }

    public function test_csv_import_fails_with_too_long_description()
    {
        Storage::fake('local');
        
        $longDescription = str_repeat('あ', 401);
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,東京都,寿司,{$longDescription},test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('long_description.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors();
    }

    public function test_csv_import_assigns_random_owner_id()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,東京都,寿司,美味しい寿司店,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('restaurants.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $restaurant = Restaurant::where('name', 'テスト店舗')->first();
        $this->assertNotNull($restaurant);
        $this->assertEquals($this->owner->id, $restaurant->owner_id);
    }

    public function test_csv_import_maps_area_and_genre_correctly()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,大阪府,焼肉,美味しい焼肉店,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('restaurants.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $restaurant = Restaurant::where('name', 'テスト店舗')->first();
        $this->assertNotNull($restaurant);
        
        $area = Area::where('name', '大阪府')->first();
        $genre = Genre::where('name', '焼肉')->first();
        
        $this->assertEquals($area->id, $restaurant->area_id);
        $this->assertEquals($genre->id, $restaurant->genre_id);
    }

    public function test_non_admin_cannot_import_csv()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "テスト店舗,東京都,寿司,美味しい寿司店,test.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('restaurants.csv', $csvContent);

        $response = $this->actingAs($this->owner)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertStatus(403);
    }

    public function test_csv_import_handles_character_encoding()
    {
        Storage::fake('local');
        
        $csvContent = "店舗名,地域,ジャンル,店舗概要,画像URL\n";
        $csvContent .= "日本料理店,東京都,居酒屋,美味しい日本料理をご提供します,japanese.jpg";
        
        $csvFile = UploadedFile::fake()->createWithContent('japanese.csv', $csvContent);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/owners/csv', [
                             'csvFile' => $csvFile
                         ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('import_complete');
        
        $this->assertDatabaseHas('restaurants', [
            'name' => '日本料理店',
            'description' => '美味しい日本料理をご提供します'
        ]);
    }
}