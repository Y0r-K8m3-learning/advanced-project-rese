# beginner-project-atte(上級模擬案件+proテスト)
:point_right:マーク : proテストで追加した機能
# アプリケーションの説明
 - 飲食店予約サービスアプリ
![image](https://github.com/user-attachments/assets/aa098673-010a-4d6b-9698-7a2d691e8d69)


## 作成した目的
 - 自社で予約サービスを展開するため

 ## アプリケーションURL
 - デプロイ用
### [店舗予約アプリ(AWS)](http://ec2-54-221-5-154.compute-1.amazonaws.com/)


 ## リポジトリURL
 - 開発用
 ### [github](https://github.com/Y0r-K8m3-learning/advanced-project-rese)
 
 ## 機能一覧
 - ログイン
 - ユーザ登録(メール認証有)
 - 店舗情報
   - :point_right: 検索
     - ソート検索(ランダム,評価が高い順, 評価が低い順)
     - 各店舗の評価平均点数が表示されます（検索する度に更新されます）
   - :point_right: 口コミ登録
      - 詳細画面:全ての口コミ表示、登録更新削除ができます
      ![image](https://github.com/user-attachments/assets/c502110f-395e-4d78-8e9e-e9a843e76fd4)
      1. 登録
         - 一般ユーザで予約終了日以降の場合に登録可能。その他の場合は、状態に合わせて画像のような表示になります
       ![image](https://github.com/user-attachments/assets/440fb162-9550-4be4-be99-25bdba257533)
       ※確認用のため、予約日が当日であれば未来時刻でもレビュー投稿が可能です。
        
      2. 更新
         - 自分の投稿のみ編集可能です。
      3. 削除
         - 投稿者または管理者が可能です。※管理者はすべての投稿を削除できます。
      
      4. 更新、削除ボタンの表示について
         - 各レビューの右上に表示されます。使用可能なユーザ以外は非表示になります
       ![image](https://github.com/user-attachments/assets/88fa4053-ee45-46cb-a7ee-72b7820e2b6a)

   - 予約
     - 決済機能(Stripe)
     
   - お気に入り
 - マイページ
   - 予約状況
      - 予約変更
      - 照会用QR
 - 権限によって以下の画面が利用可能
  　- 店舗代表者
      - 店舗登録/編集（画像保存機能有）
         
    - 管理者
      - 店舗代表者の登録
      - 利用者へのお知らせメール
      - :point_right: CSVファイルから店舗一括登録
        店舗一覧画面からcsvをアップロードして店舗を一括登録できます
        <br>※管理者ログインのトップページは店舗一覧でないため、サイトメニューから「Home」を選択して店舗一覧に遷移してください。
        <br>テスト用csvファイル(10件ダミーデータ)→[こちら](https://github.com/Y0r-K8m3-learning/advanced-project-rese/blob/main/src/test_utf.csv)
        ![image](https://github.com/user-attachments/assets/973985b4-49df-4540-a684-a4b83079c92a)

 - その他の機能
   - リマインダー：毎朝9時にその日の予約情報をメール送信
   - レスポンシブデザイン:ブレキングポイント 768px
   
## 使用技術
- PHP 8.3.7
- laravel 11.10.0
- MySQL 8.0.37


## テーブル設計 :point_right: reviews,review_images
![image](https://github.com/user-attachments/assets/802ceb72-2ea1-48bc-b41d-fe8787a4d016)

![image](https://github.com/user-attachments/assets/eed2fc3a-f7e9-4cf5-a3bc-75916bb6e64a)

![image](https://github.com/user-attachments/assets/9924792a-170b-4a77-9885-0a116fc47fbb)

## :point_right: ER図
![rese_ER](https://github.com/user-attachments/assets/fbb1de42-c467-4c7c-8c9e-eb58acca4e2b)

## 環境構築
### Docker環境で実行
### ビルドからマイグレーション、シーディングまでを記述する
- Dockerビルド 
 1. `git clone https://github.com/Y0r-K8m3-learning/advanced-project-rese.git`
 2. `cd advanced-project-rese`
 3. `cp -p　host.env .env` ※host.env：phpコンテナ内のユーザ名を変更したい場合は、更新してください
 4. `docker-compose up -d --build`
 
　※MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.ymlファイルを編集してください。
 
- Laravel環境構築
 1. `docker-compose exec php bash`
 2. `composer install`
 3. `cp -p .env.example .env`
 4. `php artisan key:generate`
 5. `php artisan migrate`
 6. `php artisan db:seed`
     - :point_right: イニシャルセットについて
       - 既定の各マスタデータ[areas,genres,roles]
       - 既定の店舗データ
       - Users: ダミーデータ 12件(一般:test_user* 10件、オーナ:test_owner 1件、管理者:test_admin 1件）
         　- 一般は test_user1～10(test_user1@example.com～test_user10@example.com)まで
         <br> パスワード：test_user1～10→ `testtest`,test_owner→`ownerowner` ,test_admin→`adminadmin`
         <br>
         ![image](https://github.com/user-attachments/assets/9c98dee9-109a-41ca-afee-2bfa60a4bac5)
       - レビュー(reviwes):ダミーデータ5件 test_user1の店舗IDが1～5の店舗のレビュー
       - 予約(reservatopms):ダミーデータ20件 test_user1の全ての店舗の予約を過去日で登録（レビュー表示、登録用）

       - 
 8. `npm install`
 9. `npm run build`
 10. 日時バッチメール設定 クーロンに下記を設定してください。
     - crontab -e
      - `* * * * * /usr/local/bin/php /var/www/artisan schedule:run >> /dev/null 2>&1`
     - ※ 手動実行する場合 /src/routes/console.php内の下記日時部分を現在時刻に変更してrunコマンドを実行してください。
       - `Schedule::command('send:reminder-emails')->dailyAt('09:00');`<br>
       - `php artisan schedule:run`<br>
     
## 本番環境(AWS)について
  ### http接続(非SSL認証)のため、ブラウザ設定によっては接続できません。
　- 検証用ユーザ
    メールサーバはmailtrapのテストサーバを使用しているため、ユーザ登録・リマインダーなどのメールはすべて開発者のmailtrapメールボックスに送信されます。
    ログインして検証する場合は以下の各権限毎に以下のユーザ情報を使用してください。
    <br>一般権限ユーザのパスワードはすべて`testtest`
    
    - 一般権限
       - メールアドレス : test_user1@example.com　～ test_user20@example.com
       - パスワード     : testtest
    - オーナ権限
       - メールアドレス : test_owner@example.com
       - パスワード     : ownerowner
    - 管理者
       - メールアドレス : test_admin@example.com
       - パスワード     : adminadmin
       
  - 決済について
    stripeのテスト機能を使用しています。こちらも決済データはすべて開発者のstripeアカウントに送信されるため確認はできません。
    カード番号は決済フォーム記載の番号　`4242 4242 4242 4242`を入力してください。
    
　　![image](https://github.com/user-attachments/assets/0ba7cda3-f37f-4b98-8f23-13c4dbe52b8e)

    
## その他
  1. OSによっては実行時にログファイル権限エラーが発生します。
 　- (stream or flie ～ Permission deinied」）エラーが発生する場合はsrc内のファイル権限を変更してください。<br>
     コマンド<br>
     `cd src`
     `sudo chmod 777 -R *`

 2. .envについて
 - DB設定はそのまま利用できます。（確認用のため明記しています）
 - 実行環境に応じて必要なメール設定を行ってください。
```plaintext
 MAIL_MAILER=
 MAIL_HOST=
 MAIL_PORT=
 MAIL_USERNAME=
 MAIL_PASSWORD=
 MAIL_ENCRYPTION=
 MAIL_FROM_ADDRESS=test@exmaple.come
 MAIL_FROM_NAME="${APP_NAME}"
```

-決済機能を設定する場合はstripeのapikeyを設定してください。
```plaintext
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=
```
 

