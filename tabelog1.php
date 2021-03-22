<?php

require 'vendor/autoload.php';
// 全体のURLを取得（あ、い。う。。。）

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
// ブラウザでのアクセス先を指定（東京 浅草）
$url = 'https://tabelog.com/sitemap/tokyo/A1311-A131102/';
// URLに移動
$driver->get($url);
sleep(5);
// URLを取得
$elements = $driver->findElements(WebDriverBy::cssSelector('.taglist a'));


$urls = [];
foreach ($elements as $element) {
    $deta = $element->getAttribute('href');
    $urls[] = $deta . "\n";
}



// 書き込みモード'w'
$fp = fopen('tabelog1.csv', 'w');
// 配列をひとつずつfputcsv関数に渡す
fputcsv($fp, $urls);
// ファイルをクローズ
fclose($fp);

//ブラウザーを閉じる
$driver->close();
