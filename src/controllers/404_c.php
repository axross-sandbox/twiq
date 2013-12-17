<?php
writeLog('404', $_SERVER['REQUEST_URI']);
setAlert('そのページは存在しません。');
header('Location: ' . APP_URL);
