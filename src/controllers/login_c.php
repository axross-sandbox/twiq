<?php
if (!isLogin() && !isset($_GET['oauth_verifier'])) {
    // ログイン処理 (TwiQ → Twitter)

    $pagetitle = 'ログイン中... - TwiQ';

    // 前回のトークン群が残っていたら削除する
    unset($_SESSION['tokens']);

    // コールバックURLを元にリクエストトークンを生成
    $twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
    $requestToken = $twitter->getRequestToken(CALLBACK_URL);

    // 後で使うのでトークン群をセッションに保管しておく
    $_SESSION['tokens']['oauth_token'] = $requestToken['oauth_token'];
    $_SESSION['tokens']['oauth_token_secret'] = $requestToken['oauth_token_secret'];

    // 処理終了後の帰り先URLをセッションに保管
    if (isset($_GET['return'])) {
        $_SESSION['tokens']['return_url'] = APP_URL . $_GET['return'];
    } else {
        $_SESSION['tokens']['return_url'] = APP_URL;
    }

    // 認証URLを生成してそこにリダイレクトする
    $authUrl = $twitter->getAuthorizeURL($requestToken['oauth_token']);
    header('Location: ' . $authUrl);

} else if (!isLogin() && isset($_GET['oauth_verifier'])) {
    // ログイン処理 (Twitter → TwiQ)

    // 保管しておいた帰り先URLを取得
    $returnUrl = $_SESSION['tokens']['return_url'];

    // セッションにとっておいたトークン群と認証情報でアクセストークンを生成
    $twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['tokens']['oauth_token'], $_SESSION['tokens']['oauth_token_secret']);
    $accessToken = $twitter->getAccessToken($_GET['oauth_verifier']);

    // ログイン用セッションを破棄
    unset($_SESSION['tokens']);

    // ユーザー情報を取得する
    $userInfo = json_decode(
        $twitter->OAuthRequest(
            'https://api.twitter.com/1.1/users/show.json',
            'GET',
            array(
                'user_id' => $accessToken['user_id'],
                'screen_name' => $accessToken['screen_name']
            )
        ), true
    );

    // PDOインスタンスを生成する
    try {
        $pdo = new PDO ('mysql:dbname=' . DB_NAME . ';host=' . DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $DB_OPTION);
    } catch (PDOException $e) {
        exit();
    }

    // idからレコードを取得
    $statement = $pdo->query("SELECT id FROM users WHERE id = '" . $userInfo['id'] . "';");
    $result = $statement->fetch();

    // 同じidのレコードが既に存在するか確認
    if (isset($result['id']) && $result['id'] > 0) {
        // 存在した場合は更新処理

        $statement = $pdo->prepare("UPDATE users SET screen_name = :screen_name, profile_image_url = :profile_image_url, oauth_token = :oauth_token, oauth_token_secret = :oauth_token_secret, lastlogin = :lastlogin WHERE id = :id;");

        $statement->bindValue(':id', $userInfo['id']);
        $statement->bindValue(':screen_name', $userInfo['screen_name']);
        $statement->bindValue(':profile_image_url', $userInfo['profile_image_url']);
        $statement->bindValue(':oauth_token', $accessToken['oauth_token']);
        $statement->bindValue(':oauth_token_secret', $accessToken['oauth_token_secret']);
        $statement->bindValue(':lastlogin', date('Y-m-d H:i:s'));
        $statement->execute();

    } else {
        // 存在しなかった場合は新規登録

        $statement = $pdo->prepare("INSERT INTO users (id, screen_name, profile_image_url, oauth_token, oauth_token_secret, lastlogin, created) VALUES (:id, :screen_name, :profile_image_url, :oauth_token, :oauth_token_secret, :lastlogin, :created);");

        $statement->bindValue(':id', $userInfo['id']);
        $statement->bindValue(':screen_name', $userInfo['screen_name']);
        $statement->bindValue(':profile_image_url', $userInfo['profile_image_url']);
        $statement->bindValue(':oauth_token', $accessToken['oauth_token']);
        $statement->bindValue(':oauth_token_secret', $accessToken['oauth_token_secret']);
        $statement->bindValue(':lastlogin', date('Y-m-d H:i:s'));
        $statement->bindValue(':created', date('Y-m-d H:i:s'));
        $statement->execute();

        writeLog('ユーザー', '@' . $userInfo['screen_name'] . 'がTwitterアカウントでユーザー登録しました。');
    }

    // セッションにユーザー情報を格納
    $_SESSION['user']['id'] = $userInfo['id'];
    $_SESSION['user']['screen_name'] = $userInfo['screen_name'];
    $_SESSION['user']['profile_image_url'] = $userInfo['profile_image_url'];

    // アラートをセット
    setAlert('ログインしました。', 'green');

    // 帰り先URLへ飛ばす
    header('Location: ' . $returnUrl);

} else {
    // それ以外

    // トークン群が残っていたら削除する
    unset($_SESSION['tokens']);

    // 一応ログアウトもしておく
    unset($_SESSION['user']);

    writeLog('不明なログイン処理');

    // トップページへ飛ばす
    header('Location: ' . APP_URL);
}
