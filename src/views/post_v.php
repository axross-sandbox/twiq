<section class="post main">
    <section class="intro">
        問題を投稿するには、「問題文」と「答えの解説(答え)」の両方を設定する必要があります。<br>
        <br>
        まずは問題文から設定してみましょう。それが終わったら、答えの解説を設定していくといいでしょう。<br>
        <br>
        全ての項目を埋めると、「問題を投稿する」ボタンが押せるようになります。
    </section>

    <section class="action">
        <a id="article_edit" class="action-edit-article blackbutton">問題文を設定する</a>
        <a id="interpretation_edit" class="action-edit-interpretation blackbutton">答えの解説を設定する</a>
    </section>

    <section class="submit">
        <div id="submit_intro" class="submit-intro">
            まだ設定が完了していない項目があります！
        </div>

        <ul id="submit_alerts" class="submit-alerts editbox-alerts">
        </ul>

        <a id="submit" class="submit-submit greenbutton button-disable">まだ投稿できません</a>

        <a class="submit-back redbutton" href="<?php url(); ?>">投稿するのをやめる</a>
    </section>

    <section class="article" style="display: none;">
        <section class="article-intro">
            <p>
            実際に表示される見た目を確認しながら、タイトルやサムネイル画像、問題文、答えを入力する欄を設定します。
            </p><p>
            赤色の斜線枠で囲まれている部分をタップすると、その項目を編集することができます。<br>
            </p>
            編集し終えたら、「もどる」ボタンで元の画面に戻ります。
        </section>

        <section class="article-meta">
            <div class="article-meta-date">??秒前</div>
            <div id="title_edit" class="editframe editframe-good">
                <h2 id="title_preview" class="article-meta-title"><?php echo $title; ?></h2>
            </div>
            <div class="article-meta-author">@<?php echo $author; ?></div>
            <div id="thumb_edit" class="editframe editframe-good">
                <img id="thumb_preview" class="article-meta-thumb" src="<?php echo APP_URL . $thumb; ?>">
            </div>
        </section>

        <section class="article-sentence">
            <div id="sentence_edit" class="editframe editframe-good">
                <div id="sentence_preview" class="article-sentence-text">
                    <p>
                        <?php echo $sentence; ?>
                    </p>
                </div>
            </div>
        </section>

        <section class="article-solve">
            <h3>この問題に答える</h3>
            <div class="article-solve-intro">
                答えを入力して下さい。
            </div>
            <div id="solve_edit" class="editframe editframe-good">
                <div class="article-solve-solve">
                    <span id="before_input_preview"><?php echo $before_input; ?></span> <input class="article-solve-solve-input" type="text" name="" placeholder="" readonly> <span id="after_input_preview"><?php echo $after_input; ?></span>
                </div>
            </div>
        </section>

        <section class="article-finish">
            <a id="article_finish" class="article-finish-button greenbutton">もどる</a>
        </section>
    </section>

    <form id="postform" action="<?php url('post'); ?>" method="post" enctype="multipart/form-data">
        <section id="title_detail" class="editbox" style="display: none;">
            <h3>問題のタイトル</h3>
            <input id="title_input" type="text" name="title" pladeholder="タイトルを入力して下さい" value="<?php echo $title; ?>">

            <ul id="title_alerts" class="editbox-alerts">
                <li class="bad">タイトルを決めてください！</li>
            </ul>

            <a id="title_finish" class="editbox-finish redbutton">もどる</a>
        </section>

        <section id="thumb_detail" class="editbox" style="display: none;">
            <h3>サムネイル画像の選択</h3>
            <img id="thumb_realtime" class="editbox-thumb-preview" src="<?php echo APP_URL . $thumb; ?>">

            <div class="editbox-selectfile blackbutton">
                画像をアップロードする
                <input id="thumb_input" class="editbox-thumb-input" type="file" accept="image/*" name="thumb">
            </div>

            <a id="thumb_finish" class="editbox-finish greenbutton">これでいい</a>
        </section>

        <section id="sentence_detail" class="editbox" style="display: none;">
            <h3>問題文の記述</h3>

            <textarea id="sentence_input" class="editbox-textarea" name="sentence" placeholder=""><?php echo $sentence; ?></textarea>

            <ul id="sentence_alerts" class="editbox-alerts">
                <li class="bad">問題文を記入しましょう！</li>
            </ul>

            <a id="sentence_finish" class="editbox-finish redbutton">もどる</a>
        </section>

        <section id="solve_detail" class="editbox" style="display: none;">
            <h3>答えの入力欄</h3>

            <legend>入力欄の前の言葉</legend>
            <input id="before_input_input" class="editbox-input" type="text" name="before_input" placeholder="入力欄の前にくる言葉" value="<?php echo $before_input; ?>">

            <legend>入力欄の後の言葉</legend>
            <input id="after_input_input" class="editbox-input" type="text" name="after_input" placeholder="入力欄の後にくる言葉" value="<?php echo $after_input; ?>">

            <ul id="phrase_input_alerts" class="editbox-alerts">
                <li class="good">よかったらもう少しこの問題に合った補足の言葉を入れませんか？</li>
            </ul>

            <h3>正解とするワード</h3>

            <legend>正解ワード</legend>
            <textarea id="words_input" class="editbox-textarea" name="words" placeholder="正解となるワードを記入して下さい。改行することで複数個指定できます。ひらがな・漢字・英語など、入力されうる単語に対してできるだけ複数個指定しましょう。"><?php echo $words; ?></textarea>

            <ul id="words_alerts" class="editbox-alerts">
                <li class="bad">正解ワードを設定しましょう！</li>
            </ul>

            <a id="solve_finish" class="editbox-finish redbutton">もどる</a>
        </section>

        <section class="interpretation" style="display: none;">
            <section class="interpretation-intro">
                <p>
                答えの解説を記述します。
                </p><p>
                模範的な解答とともに、誰にでも理解しやすく納得できるような解説を書くことを心がけると人気の問題に選ばれやすくなります。<br>
                </p>
                書き終えたら、「もどる」ボタンで元の画面に戻ります。
            </section>

            <section class="interpretation-interpretation">
                <textarea id="interpretation_input" class="editbox-textarea" name="interpretation" placeholder=""><?php echo $interpretation; ?></textarea>

                <ul id="interpretation_alerts" class="editbox-alerts">
                    <li class="bad">わかりやすい解説を書きましょう！</li>
                </ul>

                <a id="interpretation_finish" class="editbox-finish greenbutton">もどる</a>
            </section>
        </section>

        <input type="hidden" name="token" value="<?php echo $token ?>">
    </form>
</section>

<script src="assets/post.js"></script>
