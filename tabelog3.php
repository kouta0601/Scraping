<?php require './vendor/autoload.php';
// 各項目の取得

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

$url = "";
$count_url = 0;

// ファイルを開く
$file = fopen('tabelog2.csv', 'r');

// ファイルを1行ずつ出力
while ($line = fgets($file)) {
    // 文字列が含まれるかどうかチェック
    if (strpos($line, 'https') !== false) {
        $url_line = strstr($line, 'https');
        $url = $url_line;
    }

    // URLに移動
    $driver->get($url);
    sleep(3);

    // 読み込めた回数を表示
    $count_url += 1;
    $display_url = "読み込み回数" . $count_url;
    var_dump($display_url);

    // 読み込めたURLを表示
    $check_url = "取得したURL" . $url;
    var_dump($check_url);

    //お店の名前が表示されない場合、500msごとに確認して最大で60秒待つ
    $driver->wait(60, 500)->until(
        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('display-name'))
    );

    //現在のURLを取得
    $url_deta = $driver->getCurrentURL();
    $text[] = $url_deta . " ";
    sleep(1);

    // お店の名前を取得
    $shop = $driver->findElement(WebDriverBy::className('display-name'))->getText();
    $text[] = trim($shop);
    sleep(1);

    //レビューデータの有無
    try {
        if ($driver->findElement(WebDriverBy::id('js-detail-score-open'))) {
            //レビューを取得
            $review = $driver->findElement(WebDriverBy::id('js-detail-score-open'))->getText();
            if ($review == "-") {
                $text[]  = "レビュー数なし";
            } else {
                $text[]  = trim($review);
            }
        } else {
            $text[] = "データなし";
        }
    } catch (Exception $e) {
        //要素がない場合
        $text[] = "データなし";
    }
    sleep(1);

    //ジャンルの有無
    try {
        if ($driver->findElement(WebDriverBy::id('rst-data-head'))) {
            //ジャンルを取得
            $genre_text = $driver->findElement(WebDriverBy::id('rst-data-head'))->getText();
            // 特定の文字以前を削除
            $genre_text = strstr($genre_text, 'ジャンル');
            // 特定の文字以降を削除
            $result = strstr($genre_text, '予約・', true);
            $result = str_replace(PHP_EOL, '', $result);
            // 指定文字削除
            $str = str_replace('ジャンル', '', $result);
            $text[] = trim($str);
        } else {
            $text[] = "データなし";
        }
    } catch (Exception $e) {
        //要素がない場合
        $text[] = "データなし";
    }
    sleep(1);

    //「地図」の有無
    try {
        if ($driver->findElement(WebDriverBy::id('rdnavi-map'))) {
            //「地図」をクリック
            $driver->findElement(WebDriverBy::id('rdnavi-map'))->click();
            sleep(3);
            //住所が表示されない場合、200msごとに確認して最大で15秒待つ
            $driver->wait(15, 200)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('c-table--bordered'))
            );
            //住所データの有無
            try {
                if ($driver->findElement(WebDriverBy::className('c-table--bordered'))) {
                    //住所を取得
                    $address_text = $driver->findElement(WebDriverBy::className('c-table--bordered'))->getText();
                    // 特定の文字以降を削除
                    $result = strstr($address_text, '交通手段', true);
                    $result = str_replace(PHP_EOL, '', $result);
                    // 指定文字削除
                    $str = str_replace('住所', '', $result);
                    $text[] = trim($str);
                } else {
                    $text[] = "データなし";
                }
            } catch (Exception $e) {
                //要素がない場合
                $text[] = "データなし";
            }
            sleep(1);
        } else {
            $text[] = "データなし";
        }
    } catch (Exception $e) {
        //要素がない場合
        $text[] = "データなし";
    }
    sleep(1);
    $text[] = "\n";
}

// 書き込みモード'w'
$fp = fopen('tabelogdeta.csv', 'w');
// 配列をひとつずつfputcsv関数に渡す
fputcsv($fp, $text);
// ファイルをクローズ
fclose($fp);

//全てのウィンドウを閉じる
$driver->quit();
