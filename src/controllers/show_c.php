<?php
// 答えを送信された場合
if ($do === 'answer' && isset($_SESSION['user'] )) {
    require ctrl('solve');

// それ以外
} else {
    try {
        $pdo = new PDO ('mysql:dbname=' . DB_NAME . ';host=' . DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $DB_OPTION);
    } catch (PDOException $e) {
        var_dump($e);
        exit();
    }

    // DBから問題データを取得
    $statement = $pdo->query("SELECT q.id, q.title, q.thumb, q.sentence, q.before_input, q.after_input, q.answer_count, q.correct_count, u.screen_name AS author, q.created FROM quizzes AS q LEFT JOIN users AS u ON u.id = q.author_id WHERE q.id = '" . $action . "';");
    $quiz = $statement->fetch();

    if (isset($quiz['id']) && $quiz['id'] > 0) {
        // 問題のアクセス数を更新
        $pdo->query("UPDATE quizzes SET access_count = access_count +1 WHERE id = '" . $quiz['id'] . "';");

        // 「～分前」形式の時間を算出
        $quiz['created_from_now'] = convertToFuzzyTime($quiz['created']);

        // 問題データをエスケープ
        $quiz['title'] = htmlspecialchars($quiz['title'], ENT_QUOTES, 'UTF-8');
        $quiz['sentence'] = preg_replace('/\n/', "<br>\n", htmlspecialchars($quiz['sentence'], ENT_QUOTES, 'UTF-8'));
        $quiz['before_input'] = htmlspecialchars($quiz['before_input'], ENT_QUOTES, 'UTF-8');
        $quiz['after_input'] = htmlspecialchars($quiz['after_input'], ENT_QUOTES, 'UTF-8');

        // 正解率を算出
        $quiz['correct_rate'] = '0.0%';
        if ($quiz['correct_count'] != 0 && $quiz['answer_count'] != 0) {
            $quiz['correct_rate'] = strval(number_format($quiz['correct_count'] / $quiz['answer_count'] * 100, 1)) . '%';
        }

        // 既答ユーザーを取得
        $answerers = array();
        $statement = $pdo->query("SELECT u.screen_name, u.profile_image_url, a.is_correct, a.created FROM answers AS a LEFT JOIN users AS u ON u.id = a.user_id  WHERE a.quiz_id = '" . $action . "' ORDER BY a.created DESC;");
        $answerers = $statement->fetchAll();

        // 解答テーブルに同じ問題id・ユーザーidのレコードがあるか調べ、取得
        $statement = $pdo->query("SELECT user_id, is_correct, created FROM answers WHERE quiz_id = '" . $quiz['id'] . "' AND user_id = '" . $_SESSION['user']['id'] . "'");
        $result = $statement-> fetch();
        if (isset($result['user_id']) && $result['user_id'] > 0) {
            // 既に答えている

            $answered = $result;
            $answered['created_from_now'] = convertToFuzzyTime($answered['created']);
        } else {
            $answered = array();
        }

        // ページタイトルを変更
        $pagetitle = $quiz['title'] . ' - TwiQ';

        // ビューを読み込む
        require view('_header');
        require view('show');
        require view('_footer');
    } else {
        // 問題データが見つからなかった場合

        writeLog('閲覧', '問題データが見つかりませんでした。');
        setAlert('その問題は存在しないようです…。');
        header('Location: ' . APP_URL);
    }
}
