$(function() {
    var _title = false,
        _thumb = true,
        _sentence = false,
        _phrase = true,
        _words = false,
        _interpretation = false;

    function validateAllInputs() {
        var alerts = $('#submit_alerts').empty();

        $('#title_input').keyup();
        //$('#thumb_input').change();
        $('#sentence_input').keyup();
        phrase_input_change();
        $('#words_input').keyup();
        $('#interpretation_input').keyup();

        if ($('#title_input').val().length === 0) {
            $('#title_preview').text('タイトルを決めて下さい');
        } else {
            $('#title_preview').text($('#title_input').val());
        }

        if ($('#sentence_input').val().length === 0) {
            $('#sentence_preview').html('ここに問題の説明を記入します。<br>できるだけ読みやすく理解しやすい文章作りを心がけましょう。<br><br><br><br>');
        } else {
            $('#sentence_preview').html($('#sentence_input').val().replace(/\r\n|\r|\n/g, '<br>'));
        }

        if (!_title) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('・タイトルの設定が終わっていません！')
            );
        }

        if (!_sentence) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('・問題文の記述を書き終えていません！')
            );
        }

        if (!_phrase) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('・答え入力欄に不自然な部分があります！')
            );
        }

        if (!_words) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('・正解ワードが正しく設定されていません！')
            );
        }

        if (!_interpretation) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('・答えの解説を書き終えていません！')
            );
        }

        if (_title &&
            _thumb &&
            _sentence &&
            _phrase &&
            _words &&
            _interpretation) {
            $('#submit_intro')
                .text('全ての項目が設定されました！')
                .addClass('green');
            $('#submit')
                .text('問題を投稿する')
                .removeClass('button-disable');
        } else {
            $('#submit_intro')
                .text('まだ設定が完了していない項目があります！')
                .removeClass('green');
            $('#submit')
                .text('問題を投稿する')
                .addClass('button-disable');
        }
    }

    $('#article_edit').click(function() {
        $('.intro').hide(500);
        $('.action').hide(500);
        $('.submit').hide(500);

        $('.article').show(500);
    });

    $('#article_finish').click(function() {
        $('.article').hide(500);

        $('.intro').show(500);
        $('.action').show(500);
        $('.submit').show(500);
    });

    function showDetail(jQueryObj) {
        $('.article').hide(500);
        $(jQueryObj).show(500);
    }

    function hideDetail(jQueryObj) {
        $('.editbox').hide(500);
        $(jQueryObj).show(500);
    }

    $('#title_edit').click(function() {
        showDetail('#title_detail');
    });

    $('#title_input').keyup(function() {
        var val = $('#title_input').val(),
            alerts = $('#title_alerts').empty();

        if (val.length === 0) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('タイトルを決めてください！')
            );
            _title = false;
        } else if (val.length > 0 && val.length < 8) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('タイトルが短すぎます！')
            );
            _title = false;
        } else if (val.length >= 8 && val.length <= 32) {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('いいタイトルですね！')
            );
            _title = true;
        } else if (val.length > 32 && val.length < 256) {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('少し長いですね…Twitterなどで省略して表示されるかもしれません…')
            );
            _title = true;
        } else if (val.length >= 256) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('タイトルが長すぎます！')
            );
            _title = false;
        } else {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('不正な値です！')
            );
            _title = false;
        }

        if (_title) {
            $('#title_finish')
                .addClass('greenbutton')
                .removeClass('redbutton')
                .text('これでいい');
        } else {
            $('#title_finish')
                .addClass('redbutton')
                .removeClass('greenbutton')
                .text('もどる');
        }
    });

    $('#title_finish').click(function() {
        var val = $('#title_input').val();

        if (val.length === 0) {
            $('#title_preview').text('タイトルを決めて下さい');
        } else {
            $('#title_preview').text(val);
        }

        validateAllInputs();
        hideDetail('.article');
    });

    $('#thumb_edit').click(function() {
        showDetail('#thumb_detail');
    });

    $('#thumb_input').change(function() {
        var val = $('#thumb_input').prop('files')[0];

        if (val === undefined) {
            $('#thumb_realtime').attr('src', '');
            _thumb = true;
        } else if (val.type.substr(0, 6) === 'image/') {
            var _fr = new FileReader();

            _fr.onload = function() {
                $('#thumb_realtime').attr('src', _fr.result);
            }
            _fr.readAsDataURL(val);

            _thumb = true;
        } else {
            $('#thumb_realtime').attr('src', '');
            _thumb = false;
        }
    });

    $('#thumb_finish').click(function () {
        var val = $('#thumb_input').prop('files')[0];

        if (val !== undefined && val.type.substr(0, 6) === 'image/') {
            var _fr = new FileReader();
            _fr.onload = function() {
                $('#thumb_preview').attr('src', _fr.result);
            }
            _fr.readAsDataURL(val);
        } else {
            $('#thumb_preview').attr('src', 'http://192.168.33.10/assets/nowprinting.jpg');
        }

        validateAllInputs();
        hideDetail('.article');
    });

    $('#sentence_edit').click(function() {
        showDetail('#sentence_detail');
    });

    $('#sentence_input').keyup(function() {
        var val = $('#sentence_input').val(),
            alerts = $('#sentence_alerts').empty();

        if (val.length === 0) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('問題の内容を決めてください！')
            );
            _sentence = false;
        } else if (val.length > 0 && val.length < 32) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('内容が短すぎます！')
            );
            _sentence = false;
        } else if (val.length >= 32 && val.length <= 256) {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('いいですね！')
            );
            _sentence = true;
        } else if (val.length > 256 && val.length < 4096) {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('少し長いですね…読むのに疲れるかもしれません…')
            );
            _sentence = true;
        } else if (val.length >= 4096) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('長過ぎます！')
            );
            _sentence = false;
        } else {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('不正な値です！')
            );
            _sentence = false;
        }

        if (_sentence) {
            $('#sentence_finish')
                .addClass('greenbutton')
                .removeClass('redbutton')
                .text('これでいい');
        } else {
            $('#sentence_finish')
                .addClass('redbutton')
                .removeClass('greenbutton')
                .text('もどる');
        }
    });

    $('#sentence_finish').click(function() {
        var val = $('#sentence_input').val();
        var _val = val.replace(/\r\n|\r|\n/g, '<br>');

        if (val.length === 0) {
            $('#sentence_preview').html('ここに問題の説明を記入します。<br>できるだけ読みやすく理解しやすい文章作りを心がけましょう。<br><br><br><br>');
        } else {
            $('#sentence_preview').html(_val);
        }

        validateAllInputs();
        hideDetail('.article');
    });

    $('#solve_edit').click(function() {
        showDetail('#solve_detail');
    });

    function phrase_input_change() {
        var val_before = $('#before_input_input').val(),
            val_after = $('#after_input_input').val(),
            alerts = $('#phrase_input_alerts').empty();

        if (val_before.length < 2 && val_after.length < 2) {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('何を答えるのかわかりやすくするために、入力欄の前後に補足の言葉を入れましょう！'));
            _phrase = true;
        } else if (val_before === '答えは' && val_after === 'です') {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('よかったらもう少しこの問題に合った補足の言葉を入れませんか？'));
            _phrase = true;
        } else if ((val_before.length >= 2 && val_before.length <= 64 && /^[ 　]*$/.test(val_before) === false) ||
                   (val_after.length >= 2 && val_after.length <= 64 && /^[ 　]*$/.test(val_after) === false)) {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('いいですね！'));
            _phrase = true;
        } else {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('不正な値です！'));
            _phrase = false;
        }

        if (_phrase && _words) {
            $('#solve_finish')
                .addClass('greenbutton')
                .removeClass('redbutton')
                .text('これでいい');
        } else {
            $('#solve_finish')
                .addClass('redbutton')
                .removeClass('greenbutton')
                .text('もどる');
        }
    }

    $('#before_input_input').keyup(phrase_input_change);
    $('#after_input_input').keyup(phrase_input_change);

    $('#words_input').keyup(function() {
        var vals = $('#words_input').val().replace(/\n+$/, '').split("\n");
            _obj = {};
            _arr = [];
            alerts = $('#words_alerts').empty();
            wordsStrLen = 0;

        for (var i = 0, len = vals.length; i < len; i++) {
            if (/^[ 　]*$/.test(vals[i]) === false) {
                _obj[vals[i]] = vals[i];
            }
        }

        for (var i in _obj) {
            _arr.push(_obj[i]);
            wordsStrLen += _obj[i].length + 3;
        }

        if (_arr.length === 0) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('有効な正解ワードが1つもありません！')
            );
            _words = false;
        } else if (_arr.length !== 0 && _arr.length < 3) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('有効な正解ワードが少なすぎます！')
            );
            _words = false;
        } else if (_arr.length >= 3 && wordsStrLen < 256) {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('いいですね！')
            );
            _words = true;
        } else if (_arr.length >= 3 && wordsStrLen >= 256) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('答えの数が多すぎるか、それぞれが長すぎます！')
            );
            _words = false;
        } else {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('不正な値です！'));
            _words = false;
        }

        if (_arr.length !== 0 && _arr.length !== vals.length) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('(重複した正解ワードか、空行が存在するかもしれません…)'));
        }

        if (_phrase && _words) {
            $('#solve_finish')
                .addClass('greenbutton')
                .removeClass('redbutton')
                .text('これでいい');
        } else {
            $('#solve_finish')
                .addClass('redbutton')
                .removeClass('greenbutton')
                .text('もどる');
        }
    });

    $('#solve_finish').click(function() {
        var val_before = $('#before_input_input').val(),
            val_after = $('#after_input_input').val();

        $('#before_input_preview').text(val_before);
        $('#after_input_preview').text(val_after);

        validateAllInputs();
        hideDetail('.article');
    });

    $('#interpretation_edit').click(function() {
        $('.intro').hide(500);
        $('.action').hide(500);
        $('.submit').hide(500);

        $('.interpretation').show(500);
    });

    $('#interpretation_input').keyup(function() {
        var val = $('#interpretation_input').val(),
            alerts = $('#interpretation_alerts').empty();

        if (val.length === 0) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('わかりやすい解説を書きましょう！')
            );
            _interpretation = false;
        } else if (val.length > 0 && val.length < 16) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('解説が短すぎます！')
            );
            _interpretation = false;
        } else if (val.length >= 16 && val.length <= 256) {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('いいですね！')
            );
            _interpretation = true;
        } else if (val.length > 256 && val.length < 4096) {
            alerts.append(
                $('<li></li>')
                    .addClass('good')
                    .text('少し長いですね…読むのに疲れるかもしれません…')
            );
            _interpretation = true;
        } else if (val.length >= 4096) {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('長過ぎます！')
            );
            _interpretation = false;
        } else {
            alerts.append(
                $('<li></li>')
                    .addClass('bad')
                    .text('不正な値です！')
            );
            _interpretation = false;
        }

        if (_interpretation) {
            $('#interpretation_finish')
                .addClass('greenbutton')
                .removeClass('redbutton')
                .text('もどる');
        } else {
            $('#interpretation_finish')
                .addClass('redbutton')
                .removeClass('greenbutton')
                .text('もどる');
        }
    });

    $('#interpretation_finish').click(function() {
        validateAllInputs();

        $('.interpretation').hide(500);

        $('.intro').show(500);
        $('.action').show(500);
        $('.submit').show(500);
    });

    $('#submit').click(function() {
        if (_title &&
            _thumb &&
            _sentence &&
            _phrase &&
            _words &&
            _interpretation) {
            $('#postform').submit();

            $('body').append(
                $('<div></div>')
                    .css({
                        position: 'fixed',
                        left: '0px',
                        right: '0px',
                        top: '0px',
                        bottom: '0px',
                        padding: '12px',
                        'background-color': 'rgba(0, 0, 0, .8)',
                    })
                    .append(
                        $('<div></div>')
                            .css({
                                display: 'inline-block',
                                width: '128px',
                                height: '128px',
                                'margin-left': (($(window).width() - 128) / 2) + 'px',
                                'margin-top': (($(window).height() - 128) / 2) + 'px',
                                'background-image': 'url(data:image/gif;base64,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TdqxgNOoU6tezbp16tKhXcueLRs2aNq4cwuw/Vm3b9e8Pf8erjp4Z+LId2cwwLy58+fQowdPTlxD9OvYnU+n/tt69u/Qt3PX7R28eQPix+Muf/57evWz2bfH/h4+8OXz3fO2vx5/fvr78Ref/3//SReggPdhUCCAtiFYG4ELanegg6vJFyFz9VF4moUXZqghhxF6SCGIC4roIIkFmoggiv+pKCCL+bnIH4zzyWgfje3ZCB+O5+moHo/m+TgekOAJyR2R+jWoYYUQXojehEtu2GSHUEaJZHZGUnclg7BFWZxxm3n5GpiUiYkamWWaqRyajam5JptEuAlnm2rOaeedeOap55589unnn4AGKuighBZqJwCIJqrooowuCkKjkEaK6KOSVqqoCJZmWsMHmlpKaaeRYgpqqJyOCumnpjq6QqqMosrqpKW+mqiossLqQa2zxlorrbvqKqurr/L6q6/BEsuqsMXeiuumyuKK7JyxxqYKLLTTShutqdViu6qz146arbfb9tqsuJUt+6y14w6bbrLdgvqtu+12eq6261JbLrfvyhuvpvnyG66695LLwbLM7ptpvwcb7Om/7A5srsKVzgtuvegGDDDF9Frc8AYESwwvxhNrbC/IH4tcscP4QiwpwguTrC/LEatMqsv+wryyzKfi3KihPPfs889ABy300EQXbfTRSCfNcwQAOw==)'
                            })
                    )
            )
        }
    });

    validateAllInputs();
});
