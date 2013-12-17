<section class="view main">
<?php if ($correct) { ?>
    <section class="result">
        <div class="result-correct">正解!!</div>
        あなたはこの問題に正解しました！
    </section>
<?php } else { ?>
    <section class="result">
        <div class="result-wrong">残念…</div>
        あなたはこの問題の謎を解き明かせませんでした…。
    </section>
<?php } ?>

    <section class="article">
        <section class="article-meta">
            <h2 class="article-meta-title"><?php echo $quiz['title']; ?></h2>
            <span class="article-meta-author">@<?php echo $quiz['author']; ?></span>
            <img class="article-meta-thumb" src="<?php echo APP_URL . $quiz['thumb']; ?>">
        </section>

        <section class="article-answer">
            <h3>答えと解説</h3>
            <p>
                <?php echo $quiz['interpretation']; ?>
            </p>
        </section>
    </section>

    <?php if (count($friends) !== 0): ?>
    <section class="friends">
        <h3>挑戦したフレンド</h3>
        <div class="friends-intro">この問題は、あなたの他にもこんな人達が回答しています。</div>

        <?php for ($_i = 0, $_len = count($friends); $_i < $_len; $_i++): ?>
        <div class="friends-member<?php if ($_i % 2 === 0) { echo ' friends-member-odd'; } ?>">
            <?php if ($friends[$_i]['is_correct'] === '0') { ?>
            <span class="friends-member-img friends-member-img-wrong" style="background-image: url(<?php echo $friends[$_i]['profile_image_url']; ?>);"></span>
            <?php } else { ?>
            <span class="friends-member-img friends-member-img-correct" style="background-image: url(<?php echo $friends[$_i]['profile_image_url']; ?>);"></span>
            <?php } ?>
            <span class="friends-member-name">@<?php echo $friends[$_i]['screen_name']; ?></span>
            <span class="friends-member-date"><?php echo convertToFuzzyTime($friends[$_i]['created']); ?></span>
        </div>
        <?php endfor; ?>
    </section>
    <?php endif; ?>

    <section class="action">
        <a class="action-back redbutton" href="<?php url(); ?>">もどる</a>
    </section>


</section>
