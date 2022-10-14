# neu-genbo-src-01 開発環境構築手順


開発環境構築の手順を記す

# Requirement

* Docker Desktop

# Installation

#### 1. github からソースを取得

```bash
% git clone git@github.com:t-yonamine/neu-genbo-src-01.git
% cd neu-genbo-src-01
```

#### 2. パッケージのインストール

```bash
% composer install
```

#### 3. .env 作成

```bash
% cp .env.example .env
```

Docker を起動後

```bash
# sail 起動
% sail up -d

# APP_KEY 作成
% sail artisan key:generate

   INFO  Application key set successfully.  

% sail artisan config:cache

   INFO  Configuration cached successfully.  
```

#### 4. データベース作成

```bash
% mysql -h 127.0.0.1 -P 13306 -u root -p
Enter password: #パスワードなし
Welcome to the MySQL monitor.  Commands end with ; or \g.
Your MySQL connection id is 8
Server version: 8.0.30 MySQL Community Server - GPL

Copyright (c) 2000, 2022, Oracle and/or its affiliates.

Oracle is a registered trademark of Oracle Corporation and/or its
affiliates. Other names may be trademarks of their respective
owners.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

mysql> 
mysql> CREATE DATABASE genbo;
Query OK, 1 row affected (0.00 sec)
mysql> exit;
Bye

```

#### 5. テーブル作成

```bash
$ sail artisan migrate
```

#### 6. ブラウザから確認

```bash
# laravel top page
% open http://localhost/

# login page
% open http://localhost/login
```

<!--
# Usage

DEMOの実行方法など、"hoge"の基本的な使い方を説明する

```bash
git clone https://github.com/hoge/~
cd examples
python demo.py
```

# Note

注意点などがあれば書く

# Author

作成情報を列挙する

* 作成者
* 所属
* E-mail

# License
ライセンスを明示する

"hoge" is under [MIT license](https://en.wikipedia.org/wiki/MIT_License).

社内向けなら社外秘であることを明示してる

"hoge" is Confidential.
)
-->
