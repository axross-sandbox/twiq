<?php

// すべてのセッションを破棄する
session_unset();

setAlert('ログアウトしました。');

header('Location: ' . APP_URL);
