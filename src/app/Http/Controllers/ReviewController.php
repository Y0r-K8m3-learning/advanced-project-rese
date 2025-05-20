<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Http\Requests\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{

    public function create($restaurant_id)
    {
        $restaurant = Restaurant::with(['area', 'genre'])->findOrFail($restaurant_id);
        $user = Auth::user();

        $favoriteRestaurantIds = $user ? $user->favorites()->pluck('restaurant_id')->toArray() : [];
        //$user_id = Auth::id();
        //  $user_review = Review::where('user_id', $user_id)->pluck('restaurant_id')->first();

        // $reviews = Review::where('restaurant_id', $restaurant_id)->with('user')->get();


        return view('review.create', compact('restaurant', 'favoriteRestaurantIds'));
    }

    //口コミ評価登録
    public function store(Request $reviewrequest)
    {


        // レビューの保存
        $review = Review::create([
            'restaurant_id' => $reviewrequest->input('restaurant_id'),
            'user_id' => '999', //todo: ユーザーIDを取得
            'rating' => $reviewrequest->input('rating'),
            'comment' => $reviewrequest->input('comment'),
        ]);




        //レビュー画像の保存
        if ($reviewrequest->hasFile('image')) {
            $images = $reviewrequest->file('image');
            if (!is_array($images)) {
                $images = [$images]; // 単体ファイルも配列に変換
            }

            //bulk update,insert
            //画像の拡張子
            $image = $reviewrequest->file('image');
            $extension = $image->getClientOriginalExtension();
            $random = Str::random(20);
            //画像の名前を生成[

            $fileName = $reviewrequest->id . '_' . $random . '.' . $extension;
            $reviewImage = new ReviewImage();

            $reviewImage->saveImage([
                'review_id' =>  $review->id,
                'file_name' => $fileName,
                'file_path' => 'storage/review_images/' . $fileName,
                'file_type' => $image->getClientMimeType(),
                'file_size' => $image->getSize(),
                'file_mime' => $image->getClientMimeType(),
                'file_extension' => $extension,
                'file_original_name' => $image->getClientOriginalName(),
                'file_original_path' => $image->getPathname(),

            ]);
            //画像の保存
            $image->storeAs('review_images', $fileName, 'public');
        }



        return redirect()->back()->with('status', '');
    }

    public function edit($restaurant_id)
    {
        $user = Auth::user();
        // ユーザーがログインしているか確認
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }

        $restaurant = Restaurant::with(['area', 'genre'])->findOrFail($restaurant_id);
        $user_review = Review::with('restaurants')->where('id', $restaurant_id)->where('user_id', $user->id)->first();
        $user = Auth::user();
        $favoriteRestaurantIds = $user ? $user->favorites()->pluck('restaurant_id')->toArray() : [];
        return view('review.edit', compact(['user_review', 'restaurant', 'favoriteRestaurantIds']));
    }

    


    public function update(ReviewRequest $request)
    {
        $user = Auth::user();
        $restaurant_id = $request->input('restaurant_id');

        // レビューの取得
        $review = Review::where('restaurant_id', $restaurant_id)
            ->where('user_id', $user->id)
            ->firstOrFail();


        // コメント・評価の更新
        $review->comment = $request->input('comment');
        $review->rating = $request->input('rating');
        //レビューの保存
        $review->save();


        // 画像があれば処理
        if ($request->hasFile('image')) {
            //review_imagesテーブルの更新
            $reviewImage = new ReviewImage();

            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $random = Str::random(20);
            $fileName = $review->id . '_' . $random . '.' . $extension;
            // 元画像のパスを取得
            $oldImagePath = $reviewImage->where('review_id', $review->id)->pluck('file_path')->first() ?? null;

            //元画像パス


            // 新しい画像を更新
            $reviewImage->where('review_id', $review->id)->update([
                'file_name' => $fileName,
                'file_path' => 'storage/review_images/' . $fileName,
                'file_type' => $image->getClientMimeType(),
                'file_size' => $image->getSize(),
                'file_mime' => $image->getClientMimeType(),
                'file_extension' => $extension,
                'file_original_name' => $image->getClientOriginalName(),
                'file_original_path' => $image->getPathname(),
            ]);



            $image->storeAs('review_images', $fileName, 'public');

            // 元画像削除
           
            if ($oldImagePath && Storage::disk('public')->exists(str_replace('storage/', '', $oldImagePath))) {
                $s = Storage::disk('public')->delete(str_replace('storage/', '',  $oldImagePath));
            }
        }



        // 必要に応じてリダイレクトやレスポンス
        return redirect()->route('review.edit', ['restaurant_id' => $restaurant_id])
            ->with('status', '口コミを更新しました');
    }

    public function delete($restaurant_id)
    {
        $user = Auth::user();
        // ユーザーがログインしているか確認
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }

        $restaurant = Restaurant::with(['area', 'genre'])->findOrFail($restaurant_id);
        $user_review = Review::with('restaurants')->where('id', $restaurant_id)->where('user_id', $user->id)->first();
        $user = Auth::user();
        $favoriteRestaurantIds = $user ? $user->favorites()->pluck('restaurant_id')->toArray() : [];
        return view('review.delete', compact(['user_review', 'restaurant', 'favoriteRestaurantIds']));
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $restaurant_id = $request->input('restaurant_id');

        // レビューの取得
        $review = Review::where('restaurant_id', $restaurant_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // 画像の削除
        $reviewImage = new ReviewImage();
        $oldImagePath = $reviewImage->where('review_id', $review->id)->pluck('file_path')->first() ?? null;
        if ($oldImagePath && Storage::disk('public')->exists(str_replace('storage/', '', $oldImagePath))) {
            Storage::disk('public')->delete(str_replace('storage/', '',  $oldImagePath));
        }

        // レビューの削除
        $review->delete();

        return redirect()->route('index')->with('status', '口コミを削除しました');
    }
}
