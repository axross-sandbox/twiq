<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pagetitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php url('assets/style.css?' . generateRandomString()); ?>">
    <script type="text/javascript" src="<?php url('assets/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php url('assets/script.js?' . generateRandomString()); ?>"></script>
</head>
<body>
<section class="header clearfix">
    <a href="<?php url(); ?>">
        <span><h1 class="header-title">Twi<span class="green">Q</span></h1></span>
    </a>

<?php if (isLogin()): ?>
    <div class="header-user">
        <span class="header-user-img" style="background-image: url(<?php echo $_SESSION['user']['profile_image_url']; ?>);"></span>
        <span class="header-user-name">@<?php echo $_SESSION['user']['screen_name']; ?></span>
    </div>
<?php endif ?>
</section>

<section class="alerts">
<?php foreach ($ALERTS as $_a): ?>
    <div class="alerts-<?php echo $_a['color']; ?>">
        <?php echo $_a['text']; ?>
    </div>
<?php endforeach; ?>
</section>
