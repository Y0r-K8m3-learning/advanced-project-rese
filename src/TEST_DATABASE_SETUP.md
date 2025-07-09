# テストデータベースセットアップ手順

## 概要
Laravelアプリケーションのテスト実行用データベースの設定方法とテスト実行手順について説明します。

## 1. ファイル構成

### 1.1 テスト環境設定ファイル
- `.env.testing` - テスト環境用の環境変数設定
- `docker/mysql/init.sql` - MySQL初期化スクリプト
- `docker-compose.yml` - Docker設定（初期化スクリプト含む）

### 1.2 主要な設定内容

#### `.env.testing`
```env
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_test_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

#### `docker/mysql/init.sql`
```sql
CREATE DATABASE IF NOT EXISTS laravel_test_db;
GRANT ALL PRIVILEGES ON laravel_test_db.* TO 'laravel_user'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES ON laravel_test_db.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;
```

## 2. データベース作成手順

### 2.1 初回セットアップ（自動）
docker-composeにより自動的にテストデータベースが作成されます。

### 2.2 手動作成（必要に応じて）
```bash
# コンテナ内でMySQLにアクセス
docker-compose exec mysql mysql -u root -p'root'

# テストデータベース作成
CREATE DATABASE IF NOT EXISTS laravel_test_db;
GRANT ALL PRIVILEGES ON laravel_test_db.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;
```

## 3. データベース確認

### 3.1 コマンドラインでの確認
```bash
# データベース一覧確認
docker-compose exec mysql mysql -u laravel_user -p'laravel_pass' -e "SHOW DATABASES;"

# テストデータベースへのアクセス確認
docker-compose exec mysql mysql -u laravel_user -p'laravel_pass' laravel_test_db
```

### 3.2 phpMyAdminでの確認
1. ブラウザで http://localhost:8080 にアクセス
2. ログイン情報:
   - ユーザー名: `laravel_user`
   - パスワード: `laravel_pass`
3. `laravel_test_db` データベースが表示されることを確認

## 4. テスト実行手順

### 4.1 マイグレーション実行
```bash
# テスト環境でマイグレーション実行
docker-compose exec php php artisan migrate --env=testing

# マイグレーション状態確認
docker-compose exec php php artisan migrate:status --env=testing
```

### 4.2 テスト実行
```bash
# 全テスト実行
docker-compose exec php php artisan test

# 特定のテストクラス実行
docker-compose exec php php artisan test tests/Feature/ExampleTest.php

# 特定のテストメソッド実行
docker-compose exec php php artisan test --filter testExample
```

### 4.3 データベースリセット
```bash
# テストデータベースをリセット
docker-compose exec php php artisan migrate:fresh --env=testing

# シーダーも含めてリセット
docker-compose exec php php artisan migrate:fresh --seed --env=testing
```

## 5. トラブルシューティング

### 5.1 データベース接続エラー
```
SQLSTATE[HY000] [1044] Access denied for user 'laravel_user'@'%' to database 'laravel_test_db'
```

**解決方法:**
```bash
# 権限を再設定
docker-compose exec mysql mysql -u root -p'root' -e "GRANT ALL PRIVILEGES ON laravel_test_db.* TO 'laravel_user'@'%'; FLUSH PRIVILEGES;"
```

### 5.2 データベースが存在しない
```
SQLSTATE[HY000] [1049] Unknown database 'laravel_test_db'
```

**解決方法:**
```bash
# データベースを手動作成
docker-compose exec mysql mysql -u root -p'root' -e "CREATE DATABASE IF NOT EXISTS laravel_test_db;"
```

### 5.3 phpMyAdminでデータベースが見えない
- ブラウザのキャッシュをクリア
- phpMyAdminを再読み込み
- 手動でデータベースを作成後、権限を確認

## 6. 注意事項

- テストデータベースは本番データベースとは別に管理される
- テスト実行時は自動的にトランザクションがロールバックされる
- テストデータは永続化されない
- `.env.testing`ファイルは`.env`ファイルよりも優先される

## 7. ベストプラクティス

- テスト前に必ずマイグレーションを実行
- テスト間でのデータの依存関係を避ける
- ファクトリーとシーダーを活用してテストデータを管理
- テスト実行後のデータベース状態を確認する習慣をつける