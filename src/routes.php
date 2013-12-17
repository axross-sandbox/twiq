<?php
// 設定ファイル、サブルーチン定義ファイル、TwitterOAuthライブラリの読み込み
require 'functions/twitteroauth.php';
require 'functions/config.php';
require 'functions/function.php';

// セッションの使用開始
session_start();

// URIを分解
$explodedUri = explode('?', $_SERVER['REQUEST_URI']);
$_reqarr = explode('/', $explodedUri[0]);

// actionとdoの切り出し
$action = $_reqarr[1];
$do = isset($_reqarr[2]) ? $_reqarr[2] : '';

// アラートの読み込み
$ALERTS = isset($_SESSION['alerts']) ? $_SESSION['alerts'] : array();
unset($_SESSION['alerts']);

// URIルーティング
if ($action === '') {
    require ctrl('index');

} else if ($action === 'login') {
    require ctrl('login');

} else if ($action === 'logout') {
    require ctrl('logout');

} else if ($action === 'post') {
    require ctrl('post');

} else if (preg_match('/^[1-9][0-9]*$/', $action)) {
    require ctrl('show');

} else {
    require ctrl('404');
}
