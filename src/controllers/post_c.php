<?php
$author = $_SESSION['user']['screen_name'];
$author_id = $_SESSION['user']['id'];
$title = '';
$thumb = '';
$sentence = '';
$before_input = '';
$after_input = '';
$words = '';
$interpretation = '';

if (isLogin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // ログインしている & POSTメソッド

    if (isset($_SESSION['post']['token']) && isset($_POST['token']) &&
    $_SESSION['post']['token'] === $_POST['token']) {
    // トークンの照合に成功

        // トークンを破棄
        unset($_SESSION['post']);

        $errors = array();

        if (isset($_POST['title'])) {
            $title = $_POST['title'];

            if (mb_strlen($title) < 8 || mb_strlen($title) >= 256) {
                $errors['title'] = 'title length error';
            }
        } else {
            $errors['title'] = 'title is nothing';
        }

        if (isset($_FILES['thumb']) && $_FILES['thumb']['size'] !== 0) {
            $dir = 'uploads/quiz/';

            do {
                $filename = generateRandomString(8) . generateRandomString(8) . '.' . substr($_FILES['thumb']['name'], strrpos($_FILES['thumb']['name'], '.') + 1);
                $thumb = $dir . $filename;
            } while (file_exists($dir . $filename));

            move_uploaded_file($_FILES['thumb']['tmp_name'], $thumb);
        } else {
            $thumb = 'assets/nowprinting.jpg';
        }

        if (isset($_POST['sentence'])) {
            $sentence = $_POST['sentence'];

            if (mb_strlen($sentence) < 32 || mb_strlen($sentence) >= 4096) {
                $errors['sentence'] = 'sentence length error';
            }

            $sentence = preg_replace('/\r\n|\r|\n/', "\n", $sentence);
        } else {
            $errors['sentence'] = 'sentence is nothing';
        }

        if (isset($_POST['before_input'])) {
            $before_input = $_POST['before_input'];

            if (mb_strlen($before_input) >= 256) {
                $errors['before_input'] = 'before length error';
            }
        } else {
            $errors['before_input'] = 'before_input is nothing';
        }

        if (isset($_POST['after_input'])) {
            $after_input = $_POST['after_input'];

            if (mb_strlen($after_input) >= 256) {
                $errors['after_input'] = 'after length error';
            }
        } else {
            $errors['after_input'] = 'after_input is nothing';
        }

        if (isset($_POST['words'])) {
            $words = $_POST['words'];
            $wordsArr = array_unique(preg_split('/\r\n|\r|\n/', $words));

            foreach ($wordsArr as $_w) {
                if (preg_match('/^[ 　]*$/', $_w) !== 1) {
                    $_words[] = $_w;
                }
            }

            if (count($_words) >= 3) {
                $wordsStr = implode('#*$', $_words);

                if (mb_strlen($wordsStr) >= 256) {
                    $errors['words'] = 'words over 256';
                }
            } else {
                $errors['words'] = 'words lower 2 items';
            }
        } else {
        $errors['words'] = 'words is nothing';
        }

        if (isset($_POST['interpretation'])) {
            $interpretation = $_POST['interpretation'];

            if (mb_strlen($interpretation) < 16 || mb_strlen($interpretation) >= 4096) {
                $errors['interpretation'] = 'interpretation length error';
            }

            $interpretation = preg_replace('/\r\n|\r|\n/', "\n", $interpretation);
        } else {
            $errors['interpretation'] = 'interpretation is nothing';
        }

        if (count($errors) === 0) {
            // PDOインスタンスを生成
            try {
                $pdo = new PDO ('mysql:dbname=' . DB_NAME . ';host=' . DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $DB_OPTION);
            } catch (PDOException $e) {
                var_dump($e);
                exit();
            }

            $idtext = generateRandomString(12) . generateRandomString(12) . generateRandomString(8);

            // 同じタイトルの問題がないか調べる
            $statement = $pdo->prepare("SELECT id FROM quizzes WHERE idtext = :idtext;");
            $statement->bindValue(':idtext', $idtext);
            $statement->execute();
            $result = $statement->fetch();

            if (!isset($result['id']) || $result['id'] < 1) {
                // 同タイトルの問題が存在しない

                $statement = $pdo->prepare("INSERT INTO quizzes (title, thumb, sentence, before_input, after_input, words, interpretation, author_id, idtext, created) VALUES (:title, :thumb, :sentence, :before_input, :after_input, :words, :interpretation, :author_id, :idtext, :created);");
                $statement->bindValue(':title', $title);
                $statement->bindValue(':thumb', $thumb);
                $statement->bindValue(':sentence', $sentence);
                $statement->bindValue(':before_input', $before_input);
                $statement->bindValue(':after_input', $after_input);
                $statement->bindValue(':words', $wordsStr);
                $statement->bindValue(':interpretation', $interpretation);
                $statement->bindValue(':author_id', $author_id);
                $statement->bindValue(':idtext', $idtext);
                $statement->bindValue(':created', date('Y-m-d H:i:s'));
                $statement->execute();

                $statement = $pdo->prepare("SELECT id FROM quizzes WHERE idtext LIKE :idtext;");
                $statement->bindValue(':idtext', $idtext);
                $statement->execute();
                $result = $statement->fetch();
                $quiz_id = $result['id'];

                $statement = $pdo->query("UPDATE users SET lastpost = '" . date('Y-m-d H:i:s') . "' WHERE id = '" . $_SESSION['user']['id'] . "';");

                $tokens = getTokens();

                $twitter = new TwitterOAuth(
                    CONSUMER_KEY,
                    CONSUMER_SECRET,
                    $tokens['oauth_token'],
                    $tokens['oauth_token_secret']
                );

                $twitter->OAuthRequest(
                    'https://api.twitter.com/1.1/statuses/update.json',
                    'POST',
                    array(
                        'status' => '「' . $title . '」をTwiQで出題しました : ' . APP_URL . $quiz_id . ' #twiq'
                    )
                );

                writeLog('出題', $_SESSION['user']['screen_name'] . 'が問題「' . $title . '」(' . $quiz_id . ') を投稿しました。');
                setAlert('問題を投稿しました。', 'green');
                header('Location: ' . APP_URL . $id);
            } else {
                // 同タイトルの問題が既にあった

                setAlert('既に同じタイトルの問題が存在します。<br>タイトルを変えて下さい。');

                // トークンの再生成
                $_SESSION['post']['token'] = generateRandomString();
                $token = $_SESSION['post']['token'];

                // ビューを読み込む
                require view('_header');
                require view('post');
                require view('_footer');
            }
        } else {
            setAlert('投稿内容に不備がありました。<br>もう一度確認してみて下さい。');
            writeLog('投稿', 'バリデーション不一致による投稿エラーが発生しました。');

            // トークンの再生成
            $_SESSION['post']['token'] = generateRandomString();
            $token = $_SESSION['post']['token'];

            // ビューを読み込む
            require view('_header');
            require view('post');
            require view('_footer');
        }
    } else {
        // トークンの照合に失敗

        // トークンを破棄
        unset($_SESSION['post']);

        setAlert('不正な投稿が行われました。');
        writeLog('投稿', 'トークン不一致による投稿エラーが発生しました。');

        header('Location: ' . APP_URL);
    }
} else if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // PDOインスタンスを生成
    try {
        $pdo = new PDO ('mysql:dbname=' . DB_NAME . ';host=' . DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $DB_OPTION);
    } catch (PDOException $e) {
        var_dump($e);
        exit();
    }

    // 最終の投稿から5分経ってなければ弾く
    $statement = $pdo->query("SELECT lastpost FROM users WHERE id = " . $_SESSION['user']['id'] . ";");
    $result = $statement->fetch();
    if (time() - strtotime($result['lastpost']) >= 180) {
        // トークンの生成
        $_SESSION['post']['token'] = generateRandomString();
        $token = $_SESSION['post']['token'];

        // 初期値の設定
        $thumb = 'assets/nowprinting.jpg';
        $before_input = '答えは';
        $after_input = 'です';

        // ビューを読み込む
        require view('_header');
        require view('post');
        require view('_footer');
    } else {
        writeLog('投稿', '連投エラーが発生しました。');
        setAlert('さっき投稿したばかりです！<br>もう少しお待ちください！');
        header('Location: ' . APP_URL);
    }
} else {
    writeLog('投稿', 'postが直で叩かれました。');
    setAlert('問題を投稿するにはログインして下さい。');
    header('Location: ' . APP_URL);
}
