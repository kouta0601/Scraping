<?php

require 'vendor/autoload.php';
// URLからクリックしてページ飛ぶ

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

// ChromeOptionsのインスタンスの作成
$options = new ChromeOptions();
//ブラウザセッションの作成
$host = 'http://localhost:4444/wd/hub';
// chrome ドライバーの起動
$capabilities = Facebook\WebDriver\Remote\DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
$driver = Facebook\WebDriver\Remote\RemoteWebDriver::create($host, $capabilities);

// ファイルを開く
$file = fopen('tabelog1.csv', 'r');

// ファイルを1行ずつ出力
while ($line = fgets($file)) {
    // 文字列が含まれるかどうかチェック
    if (strpos($line, 'https') !== false) {
        $url_line = strstr($line, 'https');
    }
    // URLに移動
    $driver->get($url_line);
    sleep(5);
    // URLを取得
    $elements = $driver->findElements(WebDriverBy::cssSelector('.list a'));
    // $urls = [];
    foreach ($elements as $element) {
        $deta = $element->getAttribute('href');
        $urls[] = $deta . "\n";
    }
}
// 書き込みモード'w'
$fp = fopen('tabelog2.csv', 'w');
// 配列をひとつずつfputcsv関数に渡す
fputcsv($fp, $urls);
// ファイルをクローズ
fclose($fp);

// ファイルポインタをクローズ
fclose($file);

//ブラウザーを閉じる
$driver->close();
