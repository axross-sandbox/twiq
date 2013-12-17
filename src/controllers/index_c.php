<?php
if (isLogin()) {
    // ログインしている場合

    // PDOインスタンスを生成
    try {
        $pdo = new PDO ('mysql:dbname=' . DB_NAME . ';host=' . DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $DB_OPTION);
    } catch (PDOException $e) {
        //var_dump($e);
        exit();
    }

    // DBから問題一覧を取得
    $statement = $pdo->query("SELECT q.id, q.title, q.thumb, u.screen_name AS author, q.created FROM quizzes AS q LEFT JOIN users AS u ON u.id = q.author_id ORDER BY q.answer_count DESC LIMIT 10;");

    // num_answer順のクイズ一覧配列を生成
    $quizzes = array();
    foreach ($statement as $_r) {
        $quizzes[] = array(
            'id' => $_r['id'],
            'title' => htmlspecialchars($_r['title'], ENT_QUOTES, 'UTF-8'),
            'thumb_image_url' => $_r['thumb'],
            'author' => $_r['author'],
            'created' => $_r['created'],
            'created_from_now' => convertToFuzzyTime($_r['created'])
        );
    }

    // セッションIDの振り直し
    session_regenerate_id(true);

    // ビューを読み込む
    require view('_header');
    require view('index_logined');
    require view('_footer');
} else {
    // ログインしていない場合

    // ビューを読み込む
    require view('_header');
    require view('index_logouted');
    require view('_footer');
}
