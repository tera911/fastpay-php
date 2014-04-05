FastPay SDK for PHP
=======================

FastPayをPHPにより、簡単に利用するためのSDKです。

- [資料](https://fastpay.yahoo.co.jp/docs)

### Composerによるインストール

composerをインストール
```bash
curl -sS https://getcomposer.org/installer | php
```

composer.jsonファイルに以下を追記してください
```json:composer.json
{
  "require": {
    "fastpay/fastpay-php":"1.0.0"
  }
}
```

インストールが終わったら、composerのautoloaderを読み込む必要があります。
```
require 'vendor/autoload.php';
```

## 使い方

### 課金を作成する

```php
<?php

require "vendor/autoload.php";

use FastPay\FastPay;

$fastpay = new FastPay("SecretID");

// 課金を作成
$charge = $fastpay->charge->create(array(
    "amount" => 666,
    "card" => "tok_xxxxxxxxxxxxxx",
    "description" => "fastpay@example.com",
    "capture" => "false",
));

// 課金を確定
$charge = $charge->capture();

// 課金を取り消し
$charge->refund();
```

### 依存プロジェクト

- Guzzle – PHP HTTP client and framework
