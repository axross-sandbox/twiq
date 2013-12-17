<section class="view main">
    <section class="article">
        <section class="article-meta">
            <span class="article-meta-date"><?php echo $quiz['created_from_now']; ?></span>
            <h2 class="article-meta-title"><?php echo $quiz['title']; ?></h2>
            <span class="article-meta-author">@<?php echo $quiz['author']; ?></span>
            <img class="article-meta-thumb" src="<?php echo $quiz['thumb']; ?>">
        </section>

        <section class="article-content">
            <p>
            <?php echo $quiz['sentence']; ?>
            </p>
        </section>
    </section>

    <section class="stat">
        <div class="stat-meta">
            <h3>この問題の正解率</h3>
            <span class="stat-meta-text"><?php echo $quiz['correct_count']; ?> / <?php echo $quiz['answer_count']; ?> (<?php echo $quiz['correct_rate']; ?>)</span>
        </div>
        <div class="stat-users">
        <?php foreach ($answerers as $_a): ?>
            <?php if ($_a['is_correct'] === '0') { ?>
            <span class="stat-users-user stat-users-user-wrong" title="<?php echo convertToFuzzyTime($_a['created']); ?>" style="background-image: url(<?php echo $_a['profile_image_url']; ?>)"></span>
            <?php } else { ?>
            <span class="stat-users-user stat-users-user-correct" title="<?php echo convertToFuzzyTime($_a['created']); ?>" style="background-image: url(<?php echo $_a['profile_image_url']; ?>)"></span>
            <?php } ?>
        <?php endforeach ?>
        </div>
    </section>

<?php if (isLogin()) { ?>
    <?php if (count($answered) === 0) { ?>
    <section class="solve">
        <h3>この謎を解き明かす</h3>
        <span class="solve-intro">答えを入力して下さい。</span>

        <form id="answer_form" action="<?php echo url($quiz['id'] . '/answer'); ?>" method="post">
            <div class="solve-answer">
                <?php echo $quiz['before_input']; ?> <input id="solve_input" class="solve-answer-input" type="text" name="answer" placeholder="答えを入力"> <?php echo $quiz['after_input']; ?>
                <input class="nodisplay" type="text">
            </div>

            <span class="solve-intro">答えを入力すると解答できます。<br>あなたの解答はタイムラインに流れます。</span>

            <a id="submit" class="solve-submit greenbutton button-disable">解答する</a>
        </form>
    </section>

    <?php } else { ?>
    <section class="solve">
        <h3>既にこの謎は解き明かしています</h3>
        <span class="solve-intro"> </span>

        <div class="solve-solved">
            <?php if ($answered['is_correct'] === '1') { ?>
                あなたは<?php echo $answered['created_from_now'] ?>にこの問題に挑戦した際、見事この謎を解き明かしました！
                <a class="solve-solved-goanswer greenbutton spinner" href="<?php url($quiz['id'] . '/answer') ?>">答えを見る</a>
            <?php } else { ?>
                あなたは<?php echo $answered['created_from_now'] ?>にこの問題に挑戦しましたが、残念ながらこの謎を解き明かすことはできませんでした。
                <a class="solve-solved-goanswer redbutton spinner" href="<?php url($quiz['id'] . '/answer') ?>">答えを見る</a>
            <?php } ?>
        </div>
        </form>
    </section>

    <?php } ?>
<?php } else { ?>
    <section class="solve">
        <h3>この謎を解き明かす</h3>
        <span class="solve-intro">答えるにはログインして下さい。</span>

        <div class="solve-nologin">
            この問題の謎を解き明かすには、<br>まずはTwitterログインを。
            <a class="solve-nologin-login greenbutton spinner" href="./login?return=<?php echo $quiz['id']; ?>">Twitterでログイン</a>
        </div>
    </section>
<?php } ?>
</section>

<script src="<?php url('assets/view.js'); ?>"></script>
