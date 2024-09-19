<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class RestaurantConttoller extends Controller
{
    public function index(Request $request)
    {


        // 検索条件の取得
        $areaId = $request->input('area');
        $genreId = $request->input('genre');
        $name = $request->input('name');

        // クエリの構築
        $query = Restaurant::query();

        // エリアの検索条件
        if ($areaId) {
            $query->where('area_id', $areaId);
        }

        // ジャンルの検索条件
        if ($genreId) {
            $query->where('genre_id', $genreId);
        }
        // 店名の検索条件（部分一致）
        if ($name) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }


        // クエリの実行

        $restaurants = $query->with(['area', 'genre'])->get();

        // エリアとジャンルのリストを取得
        $areas = Area::all();
        $genres = Genre::all();
        // $restaurants = Restaurant::with(['area', 'genre'])->get();



        $user = Auth::user(); // 現在のユーザーを取得

        // ログインしているユーザーのお気に入りレストランのIDを取得
        $favoriteRestaurantIds = $user ? $user->favorites()->pluck('restaurant_id')->toArray() : [];

        return view(
            'restaurant',
            compact('restaurants', 'areas', 'genres', 'favoriteRestaurantIds')
        );
    }

    public function test()
    {
        $d = 1;
        return $d;
    }
    public function favorite($restaurant_id)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'not_logged_in'], 401); // 未ログインなら401エラーを返す
        }


        $user = Auth::user(); // 現在のユーザーを取得
        if (!$user->favorites()->where('restaurant_id', $restaurant_id)->exists()) {
            Favorite::create([
                'user_id' => $user->id,
                'restaurant_id' => $restaurant_id,
            ]);
        }
        return response()->json(['status' => 'added']);
    }



    public function unfavorite($restaurant_id)
    {
        $restaurant = Restaurant::findOrFail($restaurant_id); // 対象のレストランを取得
        $user = Auth::user(); // 現在のユーザーを取得

        // チェック: レコードが存在するか
        if ($user->favorites()->where('restaurant_id', $restaurant->id)->exists()) {
            // お気に入りを解除
            Favorite::where('user_id', $user->id)
                ->where('restaurant_id', $restaurant->id)
                ->delete();
        }

        return response()->json(['status' => 'removed']);
    }

    public function detail($restaurant_id)
    {
        // 指定されたIDのレストランを取得
        $restaurant = Restaurant::with(['area', 'genre'])->findOrFail($restaurant_id);

        return view('detail', compact('restaurant'));
    }

    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'number' => 'required|integer|min:1|max:10',
        ]);

        // 重複チェック
        $existingReservation = Reservation::where('restaurant_id', $request->input('restaurant_id'))
            ->where('reservation_date', $request->input('date'))
            ->where('reservation_time', $request->input('time'))
            ->first();

        if ($existingReservation) {
            return redirect()->back()->withErrors(['error' => 'この時間帯にはすでに予約が入っています。別の時間を選択してください。'])->withInput();
        }


        // 予約データの作成
        $reservation = new Reservation();
        $reservation->user_id = Auth::id(); // 現在のユーザーID
        $reservation->restaurant_id = $request->input('restaurant_id'); // 隠しフィールドまたは別の方法で渡されたレストランID
        $reservation->reservation_date = $request->input('date');
        $reservation->reservation_time = $request->input('time');
        $reservation->number_of_people = $request->input('number');
        $reservation->save();
        return redirect()->route('reservation.complete');

        // 予約完了メッセージを表示
        return redirect()->back()->with('success', '予約が完了');
    }

    public function complete()
    {
        return view('reservation_complete');
    }

    public function edit($id)
    {
        $reservation = Reservation::findOrFail($id);
        return view('edit', compact('reservation'));
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        // 入力の検証
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'number_of_people' => 'required|integer|min:1|max:10'
        ]);

        // 予約の更新
        $reservation->reservation_date = $request->input('date');
        $reservation->reservation_time = $request->input('time');
        $reservation->number_of_people = $request->input('number_of_people');
        $reservation->save();

        return redirect()->route('mypage.index')->with('status', '予約を変更しました。');
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);

        // レビューの保存
        Review::create([
            'restaurant_id' => $id,
            'user_id' => auth()->id(),
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->back()->with('status', '');
    }

    // QRコード生成メソッド
    public function showQrCode($id)
    {
        $reservation = Reservation::with(['restaurant', 'user'])->findOrFail($id);
        // QRコードの生成（予約IDを含むURL）
        $qrCode = QrCode::size(200)->generate(route('reservation.verify', $reservation->id));

        return view('qrcode', compact('reservation', 'qrCode'));
    }

    // 照合メソッド
    public function verify($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return redirect()->route('index')->withErrors(['error' => '予約が見つかりませんでした。']);
        }

        $reservation->update([
            'is_verified' => true,
            'verified_datetime' => now(),
        ]);
        $status = "照合完了";

        return view('qrcode-verify', compact('reservation', 'status'));
    }

    public function reservations($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $reservations = Reservation::where('restaurant_id', $id)->get();

        return view('reservations', compact('restaurant', 'reservations'));
    }

    public function owner(Request $request)
    {
        $owner = Auth::user();
        $restaurants = $owner->restaurant; // ログインユーザーが所有する店舗
        $areas = Area::all();
        $genres = Genre::all();

        return view('owner_restaurant', compact('restaurants', 'areas', 'genres'));
    }

    public function owner_store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'area_id' => 'required|exists:areas,id',
            'genre_id' => 'required|exists:genres,id',
            'image_url' => 'nullable|image|max:2048',
        ]);

        $restaurant = new Restaurant();
        $restaurant->name = $request->name;
        $restaurant->description = $request->description;
        $restaurant->area_id = $request->area_id;
        $restaurant->genre_id = $request->genre_id;

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('public/images');
            $restaurant->image_url = $path;
            $restaurant->image_url  = Storage::url($path);
        }

        // owner_id を追加
        $restaurant->user_id = Auth::id();

        $restaurant->save();

        return redirect()->route('owner')->with('success', '店舗が登録されました。');
    }

    public function owner_edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $areas = Area::all();
        $genres = Genre::all();
        return view('restaurants.edit', compact('restaurant', 'areas', 'genres'));
    }

    public function owner_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'area_id' => 'required|exists:areas,id',
            'genre_id' => 'required|exists:genres,id',
            'image_url' => 'nullable|image|max:2048',
        ]);

        $restaurant = Restaurant::findOrFail($id);
        $restaurant->name = $request->name;
        $restaurant->description = $request->description;
        $restaurant->area_id = $request->area_id;
        $restaurant->genre_id = $request->genre_id;

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('public/images');
            $restaurant->image_url = $path;
        }

        $restaurant->save();

        return redirect()->route('restaurants.index')->with('success', '店舗情報が更新されました。');
    }
}
