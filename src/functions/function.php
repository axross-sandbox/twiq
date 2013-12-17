<?php
/***************************************************************
    よく使う処理(関数)
***************************************************************/

/* ログインしているか確認する */
function isLogin() {
    if (isset($_SESSION['user']) &&
        isset($_SESSION['user']['id']) &&
        $_SESSION['user']['id'] > 0 &&
        isset($_SESSION['user']['screen_name']) &&
        $_SESSION['user']['screen_name'] !== '' &&
        isset($_SESSION['user']['profile_image_url']) &&
        $_SESSION['user']['profile_image_url'] !== '') {
        return true;
    }

    return false;
}

/* 指定アクションへのURLを出力する */
function url($action = '') {
    echo APP_URL . $action;
}

/* 指定controllerへのパスを返す(ショートカット関数) */
function ctrl($controller) {
    return 'controllers/' . $controller . '_c.php';
}

/* 指定viewへのパスを返す(ショートカット関数) */
function view($view) {
    return 'views/' . $view . '_v.php';
}

/* 指定文字数の[1-9a-z]のランダムな文字列を返す */
function generateRandomString($length = 12) {
    return base_convert(mt_rand(pow(36, $length - 1), pow(36, $length) - 1), 10, 36);
}

/* アラートメッセージを設定する(次の画面で表示される) */
function setAlert($text = '', $color = 'red') {
    $_SESSION['alerts'][] = array('text' => $text, 'color' => $color);
    global $ALERTS;
    $ALERTS[] = array('text' => $text, 'color' => $color);
}

/* 指定user.idまたはログインユーザーのoauth_tokenとoauth_token_secretを取得して返す */
function getTokens($id = 0) {
    global $DB_OPTION;

    if ($id === 0 && isLogin()) {
        $id = $_SESSION['user']['id'];
    }

    if ($id > 0) {
        try {
            $pdo = new PDO ('mysql:dbname=' . DB_NAME . ';host=' . DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $DB_OPTION);
        } catch (PDOException $e) {
            //var_dump($e);
            exit();
        }

        $statement = $pdo->query("SELECT oauth_token, oauth_token_secret FROM users WHERE id = '" . $id . "';");
        $result = $statement->fetch();

        if (isset($result['oauth_token']) && isset($result['oauth_token_secret'])) {
            return array(
                'oauth_token' => $result['oauth_token'],
                'oauth_token_secret' => $result['oauth_token_secret']
            );
        }
    }

    return false;
}

/* 指定時間と現在との時間差をXX分前のような文字列を返す */
function convertToFuzzyTime($time) {
    $unix = strtotime($time);
    $now = time();
    $diffSec = $now - $unix;

    if ($diffSec < 60) {
        $time = $diffSec;
        $unit = '秒前';
    } elseif ($diffSec < 3600) {
        $time = $diffSec / 60;
        $unit = '分前';
    } elseif ($diffSec < 259200) { // 72時間までは「3日」ではなく「72時間」にする
        $time = $diffSec / 3600;
        $unit = '時間前';
    } elseif ($diffSec < 2764800) {
        $time = $diffSec / 86400;
        $unit = '日前';
    } else {
        return date('n月j日', $unix);
    }

    return (int)$time . $unit;
}

function writeLog($subtitle = '', $message = '') {
    if (isLogin()) {
        $_id = $_SESSION['user']['id'];
        $_name = $_SESSION['user']['screen_name'];
    } else {
        $_id = '';
        $_name = '';
    }
    $_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
    $_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $_host = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : '';
    $_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    global $DB_OPTION;

    try {
        $pdo = new PDO ('mysql:dbname=' . DB_NAME . ';host=' . DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $DB_OPTION);
    } catch (PDOException $e) {
        // var_dump($e);
        exit();
    }

    $statement = $pdo->prepare("INSERT INTO logs (created, title, message, method, address, host, agent, referer, user_id, name)
                                VALUES (:created, :title, :message, :method, :address, :host, :agent, :referer, :user_id, :name);");
    $statement->bindValue(':created', date('Y-m-d H:i:s'));
    $statement->bindValue(':title', $subtitle);
    $statement->bindValue(':message', $message);
    $statement->bindValue(':method', $_method);
    $statement->bindValue(':address', $_address);
    $statement->bindValue(':host', $_host);
    $statement->bindValue(':agent', $_agent);
    $statement->bindValue(':referer', $_referer);
    $statement->bindValue(':user_id', $_id);
    $statement->bindValue(':name', $_name);
    $statement->execute();
}
