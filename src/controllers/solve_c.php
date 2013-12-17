<?php
// DBへ接続
try {
    $pdo = new PDO ('mysql:dbname=' . DB_NAME . ';host=' . DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $DB_OPTION);
} catch (PDOException $e) {
    var_dump($e);
    exit();
}

$correct = false;

// 問題データをDBから取得
$statement = $pdo->query("SELECT q.id, q.title, q.words, q.thumb, q.interpretation, u.screen_name AS author FROM quizzes AS q LEFT JOIN users AS u ON u.id = q.author_id WHERE q.id = '" . $action . "';");
$quiz = $statement->fetch();

// 問題データを取得できた場合
if (isset($quiz['id']) && $quiz['id'] > 0) {
    $quiz['title'] = htmlspecialchars($quiz['title'], ENT_QUOTES, 'UTF-8');
    $quiz['interpretation'] = preg_replace('/\n/', "<br>\n", htmlspecialchars($quiz['interpretation'], ENT_QUOTES, 'UTF-8'));

    // 解答テーブルに同じ問題id・ユーザーidのレコードがあるか調べる
    $statement = $pdo->query("SELECT user_id, is_correct FROM answers WHERE quiz_id = '" . $quiz['id'] . "' AND user_id = '" . $_SESSION['user']['id'] . "'");
    $result = $statement-> fetch();

    if (!isset($result['user_id']) || $result['user_id'] < 1) {
        // ない ＝ まだ答えていない

        if (isset($_POST['answer']) && $_POST['answer'] !== '') {

            $correct = false;
            $answers = preg_split('/#\*\$/', $quiz['words']);

            // 答えの照合
            $_POST['answer'] = mb_convert_kana($_POST['answer'], 'aKVC');
            $_POST['answer'] = strtolower($_POST['answer']);
            foreach ($answers as $_a) {
                $_a = mb_convert_kana($_a, 'aKVC');
                $_a = strtolower($_a);

                if ($_POST['answer'] === $_a) {
                    $correct = true;
                }
            }

            // 解答レコードの書き込み
            $statement = $pdo->prepare("INSERT INTO answers (quiz_id, user_id, answer, is_correct, created) VALUES (:quiz_id, :user_id, :answer, :is_correct, :created);");
            $statement->bindValue(':quiz_id', $quiz['id']);
            $statement->bindValue(':user_id', $_SESSION['user']['id']);
            $statement->bindValue(':answer', $_POST['answer']);
            $statement->bindValue(':is_correct', $correct);
            $statement->bindValue(':created', date('Y-m-d H:i:s'));
            $result = $statement->execute();

            // 問題の解答数と正解数を更新
            $sql = "UPDATE quizzes SET answer_count = answer_count +1";
            if ($correct === true) {
                $sql .= ", correct_count = correct_count +1";
            }
            $sql .= " WHERE id = '" . $quiz['id'] . "';";
            $pdo->query($sql);

            $tokens = getTokens();
            $twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $tokens['oauth_token'], $tokens['oauth_token_secret']);

            // Twitterへ書き込み
            if ($correct === true) {
                $twitter->OAuthRequest(
                    'https://api.twitter.com/1.1/statuses/update.json',
                    'POST',
                    array(
                        'status' => '「' . $quiz['title'] . '」の謎を解き明かしました！ ' . APP_URL . $quiz['id'] . ' #twiq'
                    )
                );
            } else {
                $twitter->OAuthRequest(
                    'https://api.twitter.com/1.1/statuses/update.json',
                    'POST',
                    array(
                        'status' => '「' . $quiz['title'] . '」の謎を解き明かせませんでした…。 ' . APP_URL . $quiz['id'] . ' #twiq'
                    )
                );
            }
        } else {
            // まだ答えていないのにanswerが呼ばれた

            writeLog('解答', '空欄で送信されました。');
            setAlert('答えの内容が正常でないようです…。');
            header('Location: ' . APP_URL);
        }
    } else {
        $correct = (boolean)$result['is_correct'];
    }

    // フォロー一覧を取得
    $tokens = getTokens();
    $twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $tokens['oauth_token'], $tokens['oauth_token_secret']);
    $friends = json_decode(
        $twitter->OAuthRequest(
            'https://api.twitter.com/1.1/friends/ids.json',
            'GET',
            array()
        ), true
    );

    // フォローのidと一致する解答レコードを取得
    if (count($friends['ids']) > 0) {
        $sql = "SELECT u.profile_image_url, u.screen_name, a.is_correct, a.created FROM answers AS a LEFT JOIN users AS u ON u.id = a.user_id WHERE a.quiz_id = '" . $quiz['id'] . "'";
        for($_i = 0, $_len = count($friends['ids']); $_i < $_len; $_i++) {
            if ($_i === 0) {
                $sql .=  " AND (user_id = '" . $friends['ids'][$_i] . "'";
            } else if ($_i === $_len - 1) {
                $sql .= ")";
            } else {
                $sql .=  " OR user_id = '" . $friends['ids'][$_i] . "'";
            }
        }
        $sql .= " ORDER BY a.created DESC LIMIT 20;";
var_dump($sql);
        $statement = $pdo->query($sql);
        $friends = $statement->fetchAll();
    } else {
        $friends = array();
    }

    // ページタイトルを変更
    $pagetitle = $quiz['title'] . ' - TwiQ';

    // ビューを読み込む
    require view('_header');
    require view('solve');
    require view('_footer');
} else {
    // 問題データが見つからなかった

    writeLog('解答', '問題データが見つかりませんでした。');
    setAlert('その問題は存在しないようです…。');
    header('Location: ' . APP_URL);
}
