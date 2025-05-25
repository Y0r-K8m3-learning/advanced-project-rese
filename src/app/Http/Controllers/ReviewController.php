<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Http\Requests\ReviewRequest;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ReviewController extends Controller
{

    //口コミ一覧
    public function show($restaurant_id)
    {
        $user_id = Auth::id();

        $reviews = Review::with(['restaurant', 'image'])
            ->where('restaurant_id', $restaurant_id)
            ->orderByRaw('user_id = ? DESC, created_at DESC', [$user_id])
            ->get();
        return response()->json($reviews);
    }

    //口コミ登録表示
    public function create($restaurant_id)
    {
        $restaurant = Restaurant::with(['area', 'genre'])->findOrFail($restaurant_id);
        $user = Auth::user();

        $favoriteRestaurantIds = $user ? $user->favorites()->pluck('restaurant_id')->toArray() : [];

        $maxLength = \App\Http\Requests\ReviewRequest::MAX_REVIEW_LENGTH;

        return view('review.create', compact('restaurant', 'favoriteRestaurantIds', 'maxLength'));
    }

    //口コミ評価登録
    public function store(ReviewRequest $reviewrequest)
    {
        $restaurant_id = $reviewrequest->input('restaurant_id');
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }



        //存在チェック
        if (Review::isExists($restaurant_id, $user->id)) {
            return back()->with('error', 'この店舗の口コミは既に投稿済みです')->withInput();
        }

        //予約終了日時以降か
        if (!Reservation::isPastReservationExists($restaurant_id, $user->id)) {
            return back()->with('error', '予約終了日以降に口コミを投稿してください。')->withInput();
        }

        DB::beginTransaction();
        try {
            // レビューの保存
            $review = Review::create([
                'restaurant_id' => $reviewrequest->input('restaurant_id'),
                'user_id' => $user->id,
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('レビュー登録エラー: ' . $e->getMessage());
            return redirect()->back()->with('error', '口コミの登録に失敗しました。');
        }


        return redirect()->route('review.complete', ['restaurant_id' => $restaurant_id])->with('complete', '口コミを投稿しました');
    }

    //店舗情報,レビュー情報,お気に入り情報を取得
    private function getRestaurantAndReview($restaurant_id, $review_id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください');
        }
        $restaurant = Restaurant::with(['area', 'genre'])->findOrFail($restaurant_id);

        $user_review = Review::with(['image'])->where('id', $review_id)->first();

        $favoriteRestaurantIds = $user ? $user->favorites()->pluck('restaurant_id')->toArray() : [];

        return [$restaurant, $user_review, $favoriteRestaurantIds];
    }

    //口コミ編集
    public function edit($restaurant_id, $review_id)
    {
        [$restaurant, $user_review, $favoriteRestaurantIds] = $this->getRestaurantAndReview($restaurant_id, $review_id);
        return view('review.edit', compact(['user_review', 'restaurant', 'favoriteRestaurantIds']));
    }

    public function update(ReviewRequest $request)
    {
        $user = Auth::user();
        $restaurant_id = $request->input('restaurant_id');
        DB::beginTransaction();
        try {
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

                // 更新画像の保存
                $image->storeAs('review_images', $fileName, 'public');

                // 元画像を削除
                if ($oldImagePath && Storage::disk('public')->exists(str_replace('storage/', '', $oldImagePath))) {
                    $s = Storage::disk('public')->delete(str_replace('storage/', '',  $oldImagePath));
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('レビュー更新エラー: ' . $e);
            return redirect()->route('review.complete', ['restaurant_id' => $restaurant_id])->with('error', '口コミの更新に失敗しました。')->withInput();
        }

        return redirect()->route('review.complete', ['restaurant_id' => $restaurant_id])->with('complete', '口コミを更新しました');
    }

    public function complete($restaurant_id)
    {
        return view('review.complete', compact('restaurant_id'));
    }


    //削除画面
    public function delete($restaurant_id, $review_id)

    {

        [$restaurant, $user_review, $favoriteRestaurantIds] = $this->getRestaurantAndReview($restaurant_id, $review_id);

        return view('review.delete', compact(['user_review', 'restaurant', 'favoriteRestaurantIds']));
    }

    //口コミ削除
    public function destroy(Request $request)
    {

        $user = Auth::user();
        $review_id = $request->input('review_id');
        $restaurant_id = $request->input('restaurant_id');


        if ($user->role_id == User::ROLE_USER) {

            //一般ユーザーの場合 他ユーザの口コミの削除エラー
            $review = Review::where('id', $review_id)
                ->where('user_id', $user->id)
                ->first();
            if (!$review) {
                return redirect()->back()->with('error', 'この口コミは削除できません');
            }
        }

        DB::beginTransaction();

        try {
            $reviewImage = ReviewImage::where('review_id', $review_id)->first();
            $oldImagePath = null;
            if ($reviewImage) {
                $oldImagePath = $reviewImage->file_path;
                $reviewImage->delete();
            }
            Review::where('id', $review_id)
                ->delete();

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('レビュー削除エラー: ' . $e->getMessage());
            return redirect()->route('review.complete', ['restaurant_id' => $restaurant_id])->with('error', '口コミの削除に失敗しました。');
        }

        if ($oldImagePath && Storage::disk('public')->exists(str_replace('storage/', '', $oldImagePath))) {
            Storage::disk('public')->delete(str_replace('storage/', '',  $oldImagePath));
        }

        return redirect()->route('review.complete', ['restaurant_id' => $restaurant_id])->with('complete', '口コミを削除しました');
    }
}
