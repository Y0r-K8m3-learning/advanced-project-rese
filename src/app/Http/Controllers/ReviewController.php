<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Http\Requests\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Container\Attributes\Auth;
use App\Models\Restaurant;

class ReviewController extends Controller
{

    public function index($restaurant_id)
    {
        $restaurant = Restaurant::find($restaurant_id);

        //$user_id = Auth::id();
        //  $user_review = Review::where('user_id', $user_id)->pluck('restaurant_id')->first();

        // $reviews = Review::where('restaurant_id', $restaurant_id)->with('user')->get();


        return view('review', compact('restaurant'));
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
            $shopImageModel = new ReviewImage();

            $shopImageModel->saveImage([
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
}
