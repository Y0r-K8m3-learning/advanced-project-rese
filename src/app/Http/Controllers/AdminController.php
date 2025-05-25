<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantCsvImportRequest;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // オーナー一覧画面
    public function index()
    {
        $owners = User::where('role_id', User::ROLE_OWNER)->get();
        $users = User::where('role_id', User::ROLE_USER)->get();
        return view('admin.owners.index', compact('owners', 'users'));
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => User::ROLE_OWNER,
        ]);


        return redirect()->route('admin.owners.index')->with('success', 'オーナーが登録されました');
    }

    //CSV import処理
    public function csvImportStore(RestaurantCsvImportRequest  $request)
    {

        $file = $request->file('csvFile');


        //ファイル内容バリエーション用
        $targetAreas = ['東京都', '大阪府', '福岡県'];
        $targetGenre = ['寿司', '焼肉', 'イタリアン', '居酒屋'];
        $rules = [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:400',
            'area' => 'required|string|in:' . implode(',', $targetAreas),
            'genre' => 'required|string|in:' . implode(',', $targetGenre),
            'image_url' => ['required', 'string', 'regex:/\.(jpg|jpeg|png)$/i', 'max:255',]
        ];

        $messages = [
            'name.required' => '店舗名は必須です',
            'name.max' => '店舗名は50文字以内で入力してください。',
            'description.required' => '店舗概要は必須です',
            'description.max' => '店舗概要は400文字以内で入力してください。',
            'area.required' => '地域は必須です',
            'area.in' => '地域は東京都、大阪府、福岡県のいずれかを指定してください',
            'genre.in' => 'ジャンルは寿司、焼肉、イタリアン、居酒屋のいずれかを指定してください',
            'genre.required' => 'ジャンルは必須です',
            'image_url.regex' => '画像URLはjpg, jpeg, pngの拡張子を指定してください',
            'image_url.max' => '画像ファイルは2MB以下である必要があります',
        ];

        $path = $file->getRealPath();
        // ファイルを開く
        $filedata = fopen($path, 'r');

        $errors = [];

        DB::beginTransaction();

        try {

            // ヘッダー行
            $header = fgetcsv($filedata);

            $encarray = [];
            foreach ($header as $value) {
                //文字コード変換
                $encarray[] = $this->getEncoding($value);
            }

            // ヘッダー行チェック
            if (count($encarray) !== 5 || $encarray[0] !== '店舗名' || $encarray[1] !== '地域' || $encarray[2] !== 'ジャンル' || $encarray[3] !== '店舗概要' || $encarray[4] !== '画像URL') {
                DB::rollBack();
                fclose($filedata);

                return back()->with('error', 'CSVファイルのヘッダー情報が正しくありません。');
            }

            //マスタTBLB id変換用
            $areas = Area::whereIn('name',  $targetAreas)->pluck('id', 'name')->toArray();

            $genre = Genre::whereIn('name',  $targetGenre)->pluck('id', 'name')->toArray();

            //オーナ権限ユーザの取得
            $owner = User::where('role_id', User::ROLE_OWNER)->pluck('id')->toArray();

            $rowindex = 1; //行番号

            while (($csvData = fgetcsv($filedata)) !== FALSE) {
                $rowindex++;
                $validatedData = [
                    'name'        => $this->getEncoding($csvData[0]),
                    'area'     => $this->getEncoding($csvData[1]),
                    'genre'    => $this->getEncoding($csvData[2]),
                    'description' => $this->getEncoding($csvData[3]),
                    'image_url'   => $this->getEncoding($csvData[4]),
                ];

                $validator = Validator::make($validatedData, $rules, $messages);
                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $msg) {
                        // $i+1 で行番号（1始まり）を付与
                        $errors[] =  $msg;
                    }
                    break;
                }

                $restaurant = new Restaurant();
                $restaurant->name = $validatedData['name'];
                $restaurant->description = $validatedData['description'];
                $restaurant->image_url = $validatedData['image_url'];
                $restaurant->area_id = $areas[$this->getEncoding($csvData[1])];
                $restaurant->genre_id = $genre[$this->getEncoding($csvData[2])];

                //オーナはランダムに取得
                $restaurant->owner_id = $owner[array_rand($owner)];

                $restaurant->save();
            }
            if (count($errors) > 0) {
                DB::rollBack();
                return back()->with('error', 'ファイル内容にエラーがあります(ファイル' . $rowindex . '行目)')
                    ->withErrors($errors);
            }

            DB::commit();

            return redirect()->route('home')->with('import_complete', 'CSVファイルの店舗情報が登録されました');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSV Import Error: ' . $e->getMessage());
            // エラーが発生した場合は、エラーメッセージを表示
            return back()->with('error', 'CSVファイルのインポート中にエラーが発生しました: ');
        } finally {
            // ファイルを閉じる
            if (is_resource($filedata)) {
                fclose($filedata);
            }
        }
    }

    private function getEncoding($text)
    {
        $encoding = mb_detect_encoding($text, ['UTF-8', 'SJIS', 'SJIS-win', 'EUC-JP', 'ASCII'], true);
        if ($encoding !== 'UTF-8') {
            $text = mb_convert_encoding($text, 'UTF-8', $encoding);
        }

        return $text;
    }
}
