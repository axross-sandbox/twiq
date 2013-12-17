<?php
/***************************************************************
    全体設定
***************************************************************/

// デフォルトページタイトルの設定
$pagetitle = 'TwiQ - さあ、謎を解き明かそう。';

// 開発用のエラー表示をするか
const SHOW_ERROR = true;

// 環境ごとの設定を読み込む
require 'functions/keys_develop.php';

// PDO接続時のオプション
$DB_OPTION = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',  // PHP5.3以前のみ
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

// php.ini設定項目の書き換え
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
date_default_timezone_set('Asia/Tokyo');
ini_set('display_errors', 1);

if (SHOW_ERROR === true) {
    error_reporting(E_ALL | E_STRICT);
} else {
    error_reporting(E_ALL ^ E_NOTICE); // E_DEPRECATEDも書くべきか？
}
