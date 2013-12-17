<section class="index main">
    <section class="title">
        <h1 class="title-text">Twi<span class="green">Q</span></h1>
        <span class="title-intro">
            <p>
                さあ、謎を解き明かそう。
            </p>
        </span>
    </section>

    <section class="qlist">
        <h3>人気の問題を解く</h3>
        <ul>
            <?php foreach ($quizzes as $_q): ?>
            <a href="<?php url($_q['id']); ?>">
                <li class="qlist-item" style="background-image: linear-gradient(top, rgba(0, 0, 0, .4), rgba(0, 0, 0, .6)), url(<?php echo $_q['thumb_image_url']; ?>); background-image: -webkit-linear-gradient(top, rgba(0, 0, 0, .4), rgba(0, 0, 0, .6)), url(<?php echo $_q['thumb_image_url']; ?>);">
                    <span class="qlist-item-date"><?php echo $_q['created_from_now']; ?></span>
                    <span class="qlist-item-title"><?php echo $_q['title']; ?></span>
                    <span class="qlist-item-author">@<?php echo $_q['author']; ?></span>
                </li>
            </a>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="action">
        <h3>その他</h3>
        <a class="action-post greenbutton" href="<?php url('post'); ?>">新しい問題を投稿する</a>
        <a class="action-logout redbutton" href="<?php url('logout'); ?>">TwiQからログアウト</a>
    </section>
</section>
