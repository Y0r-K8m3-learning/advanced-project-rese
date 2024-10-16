# beginner-project-atte(上級模擬案件)
# アプリケーションの説明
 - 飲食店予約サービスアプリ
![image](https://github.com/user-attachments/assets/9fec8b38-4109-428e-962c-a96c27afa043)

## 作成した目的
 - 自社で予約サービスを展開するため

 ## アプリケーションURL
 - デプロイ用
### [Atte](http://ec2-57-180-199-228.ap-northeast-1.compute.amazonaws.com/)

 ## リポジトリURL
 - 開発用
 ### [github](https://github.com/Y0r-K8m3-learning/beginner-project-atte.git)

 ## 機能一覧
 - ログイン
 - ユーザ登録(メール認証有)
 - 店舗一覧
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
 - その他の機能
   - リマインダー：毎朝9時にその日の予約情報をメール送信
   - レスポンシブデザイン:ブレキングポイント 768px 
## 使用技術
- PHP 8.3.7
- laravel 11.10.0
- MySQL 8.0.37


## テーブル設計
![image](https://github.com/user-attachments/assets/e9b2b50a-e2fe-4569-9643-5314d5b14390)

![image](https://github.com/user-attachments/assets/1c12c735-ce30-4732-89b1-cb9c33dad35f)



## ER図
![image](https://github.com/user-attachments/assets/69447efa-8ba4-4753-9e72-404bf86bf78d)





## 環境構築
### Docker環境で実行
### ビルドからマイグレーション、シーディングまでを記述する
- Dockerビルド 
 1. `git clone https://github.com/Y0r-K8m3-learning/advanced-project-rese.git`
 2. `cd advanced-project-rese`
 3. `docker-compose up -d --build`
 
　※MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.ymlファイルを編集してください。
 
- Laravel環境構築
 1. `docker-compose exec php bash`
 2. `composer install`
 3. `cp -p .env.example .env`
 4. `php artisan key:generate`
 5. `php artisan migrate`
 6. `php artisan db:seed`
     -イニシャルセットについて
      - 各マスタデータ[areas,genres,roles]
      - 既定の店舗データ
      - Users ダミーデータ 10件。
 8. `npm run build`

## 本番環境(AWS)について
  ### http接続(非SSL認証)のため、ブラウザ設定によっては接続できません。
　- 検証用ユーザ
    メールサーバはmailtrapのテストサーバを使用しているため、ユーザ登録・リマインダーなどのメールはすべて開発者のmailtrapメールボックスに送信されます。
    ログインして検証する場合は以下の各権限毎に以下のユーザ情報を使用してください。
    
    - 一般権限
       - メールアドレス :`test_user@example.com`
       - パスワード     :`testtest`
    - オーナ権限
       - メールアドレス :`test_owner@example.com`
       - パスワード     :`ownerowner`
    - 管理者
       - メールアドレス :`test_admin@example.co`
       - パスワード     :`adminadmin`
       
  - 決済について
    stripeのテスト機能を使用しています。こちらも決済データはすべて開発者のstripeアカウントに送信されます。
    処理を行う場合はカード番号は決済フォーム記載の番号　`4242 4242 4242 4242`を入力してください。
　　![image](https://github.com/user-attachments/assets/0ba7cda3-f37f-4b98-8f23-13c4dbe52b8e)

    
## その他
  1. OSによっては実行時にログファイル権限エラーが発生します。
 　- (stream or flie ～ Permission deinied」）エラーが発生する場合は下記コマンドを実行してください。<br>
     `sudo chmod 777 -R storage`

 2. .envについて
 - DB設定はそのまま利用できます。（確認用のため明記しています）
 - 実行環境に応じて必要なメール設定を行ってください。
`
 MAIL_MAILER=
 MAIL_HOST=
 MAIL_PORT=
 MAIL_USERNAME=
 MAIL_PASSWORD=
 MAIL_ENCRYPTION=
 MAIL_FROM_ADDRESS=test@exmaple.come
 MAIL_FROM_NAME="${APP_NAME}"
`

-決済機能を設定する場合はstripeのapikeyを設定してください。
`
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=
`
 

