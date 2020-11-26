// <!-- ライトボックス -->
;
(function ($) {
    $(function () {
        $('a.swipe').photoSwipe();
    });
})(jQuery);

// <!-- Breakpoint -->

$(function () {
    $(window).setBreakpoints({
        distinct: true,
        breakpoints: [1, 769]
    });
    $(window).bind('enterBreakpoint769', function () {
        $('.sp-img').each(function () {
            $(this).attr('src', $(this).attr('src').replace('-sp', '-pc'));
        });
    });
    $(window).bind('enterBreakpoint1', function () {
        $('.sp-img').each(function () {
            $(this).attr('src', $(this).attr('src').replace('-pc', '-sp'));
        });
    });
});

// <!-- 流れる文字-->

$(function () {
    $('p.ticker').marquee();
});
$(function () {
    var imgNum = 4;
    var imgSize = 468;
    var time = 3000;
    var current = 1;
    setInterval(function () {
        if (current < imgNum) {
            current++;
            $("#banner ul").animate({
                marginLeft: parseInt($("#banner ul").css("margin-left")) - imgSize + "px"
            }, "fast");
        } else {
            $("#banner ul").animate({
                marginLeft: parseInt($("#banner ul").css("margin-left")) + (imgSize * (imgNum - 1)) + "px"
            }, "fast");
            current = 1;
        }
    }, time);
});

// <!-- アコーディオン -->

$(function () {
    $("dl").on("click", "dt", function () {
        $("dd").not($(this).next()).slideUp()
            .prev().removeClass("active");
        $(this).next().slideToggle()
            .end().toggleClass("active");
    });
});

// <!-- スムーズスクロール部分の記述 -->
$(function () {
    // #で始まるアンカーをクリックした場合に処理
    $('a[href^=#]').click(function () {
        // スクロールの速度
        var speed = 400; // ミリ秒
        // アンカーの値取得
        var href = $(this).attr("href");
        // 移動先を取得
        var target = $(href == "#" || href == "" ? 'html' : href);
        // 移動先を数値で取得
        var position = target.offset().top;
        // スムーススクロール
        $('body,html').animate({
            scrollTop: position
        }, speed, 'swing');
        return false;
    });
});