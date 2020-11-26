<?php
require_once('./admin/system/library.php');
$product = new Product();
$order = new Order();
//商品一覧の取得
if (($product_list = $product->getProductList()) === false) {
    header('Location:error.php?message=system');
    exit;
}

//商品をカートに入れる
if (!empty($_POST['cart'])) {
    //ログインしていなかった場合はカートに入れる商品データを保持しながらログイン画面へ
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php', true, 307);
        exit;
    }
    if (!$order->addProductInCart($_SESSION['user_id'], $_POST['product_id'], $_POST['num'])) {
        header('Location:error.php?message=system');
        exit;
    }
    header('Location: cart.php');
    exit;
}
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php'); ?>
<!-- メイン -->
<section class="mainview">
    <img src="img/mainview.jpg" alt="メイン">
</section>
<section class="mainview-info">
    <p>ダイエットしたいけど食欲が抑えられない。運動が苦手。健康にも不安がある貴女へ提案いたします。<br>
        置き換えダイエットと違い、食事はいつも通り<br>
        <span class="font2">1日2回程度、コップ１杯のお水と共に飲むだけ</span><br>
        特別な食事制限は不要です！
        国と沖縄県の補助金を受け研究開発、冬瓜のみを原料とし、ダイエット食品として使えるのは<br>
        <span class="font3">1つの冬瓜（約３kg）</span>からわずか<span class="font4">40gだけ！</span>ダイヤモンドのように希少で、<br>
        放送以来<span class="font5">240,000</span><span class="font4">食以上突破！</span>冬瓜ダイエットをお届けいたします。
    </p>
</section>
<!-- 冬瓜ダイエットとは -->
<section class="tougan">
    <div class="image-box">
        <img src="img/tougan-pc.jpg" class="sp-img" alt="トウガンダイエット"><br>
    </div>
    <div class="image_photo">
        <a href="img/newspaper.jpg" class="swipe" rel="group1"><img src="img/newspaper.jpg" alt="" /></a>
    </div>
    <div class="image-comment">
        <p class="info">冬瓜ダイエットは、冬瓜のみを原料としています。<br>
            冬瓜は、ウリ科の植物で原産地はジャワと言われ、<br>
            日本には中国から朝鮮半島を経て4世紀ごろ伝わってきました。<br>
            冬瓜の約98%以上は水分ですが、水分を飛ばした残りの、<br>
            わずか1.3%にはタンパク質、食物繊維、カルシウム、カリウム、<br>
            ビタミン（B1、B2、C)、鉄分等が含まれています。<br></p>
        <p class="info">国と沖縄県の補助金を受け、琉球大学ではラットを使った研究<br>
            医療法人和楽会では18名のモニターによる評価試験を行いました。<br>
            研究成果は、左の琉球新報記事をご覧ください。</p>
    </div>
</section>
<div class="contents-container">
    <!-- スモールコンテンツ -->
    <!-- 実証 -->
    <section class="proof">
        <div class="proof-title">
            <h2>開発者　中島千鶴子<br>自身が身を持って実証</h2>
        </div>
        <div class="proof-img">
            <img src="img/proof.jpg" alt="プルーフ">
        </div>
        <div class="proof-point-1">
            <p>冬瓜ダイエットで</p>
            <h3>健康的にダイエット</h3>
        </div>
        <div class="proof-comment-1">
            <h3>何度もダイエットに失敗、<br>リバウンドを繰り返し<br>70kgまで体重増加した店長が…</h3>
            <p>リバウンドを繰り返し、無理なダイエットに慣れてしまった体でも、無理なく17kgのダイエットに成功!冬瓜に秘められた驚異のパワーで成功者続出です!</p>
        </div>
        <div class="proof-point-2">
            <p>冬瓜ダイエットで</p>
            <h3>サラバ！置き換え式ダイエット</h3>
        </div>
        <div class="proof-comment-2">
            <h3>あなたはいつまで<br>置き換え式ダイエットを<br>続けるつもりですか？</h3>
            <p>三食のうち一食をダイエット食品で済ます「置き換え式 ダイエット」食べたいものを我慢し続ける置き換え式ダイエットから解放されませんか？</p>
        </div>
    </section>
    <!-- ブログ -->
    <section class="blog-container">
        <div class="blog-box">
            <div class="blog-info">
                <h2>冬瓜ダイエット ブログ　公開中!</h2>
                <p>冬瓜ダイエット開発者 中島千鶴子が<span class="font6">直伝!</span></p>
                <h3>冬瓜でDiet!</h3>
            </div>
            <div class="blog-btn">
                <a href="http://sakurabussan.com/">
                    <p>ブログはコチラ<i class="fa fa-chevron-circle-right" aria-hidden="true"></i></p>
                </a>
            </div>
        </div>
    </section>
    <!-- プロフィール -->
    <section class="profile">
        <div class="profile-contents">
            <h2>「今まで色々ありました…」</h2>
            <p>冬瓜ダイエット開発者 中島千鶴子の人生を<span class="font7">赤裸々に告白!</span></p>
            <div class="profile-link"><a href="http://www.tougan-diet.com/profile.html">
                    <p>開発者の衝撃プロフィールはコチラ<i class="fa fa-chevron-circle-right" aria-hidden="true"></i></p>
                </a></div>
        </div>
    </section>
    <!-- yahoo!news -->
    <section class="yahoo-news-container">
        <div class="yahoo-news">
            <div class="news-box">
                <p>Yahoo!ニュースの<span class="font8">サイエンス部門のトップニュースで<br></span>
                    取り上げられた「あの」話題の製品が</p>
            </div>
            <div class="news">
                <h2>遂に商品化!</h2>
            </div>
            <div class="yahoo-news-info">
                <p><span class="font9">3kgの冬瓜から1.3%…わずか40gしか取ることが</span></p>
                <p><span class="font9">出来ない無添加の100%冬瓜粉末を</span></p>
                <p>みなさまの健康にお役立て下さい。</p>
            </div>
            <div class="article-btn"><a href="http://ryukyushimpo.jp/photo/prentry-29353.html">
                    <p>関連記事はコチラ<i class="fa fa-chevron-circle-right" aria-hidden="true"></i></p>
                </a></div>
        </div>
    </section>
    <!-- 新製品 -->
    <section class="new-container">
        <div class="new-box">
            <div class="new-product">
                <p>新製品</p>
            </div>
        </div>
        <div class="new-title">
            <h3>またまた中小企業庁の<br><span class="font10">補助事業に採択されました!</span></h3>
        </div>
        <div class="new-comment">
            <p>冬瓜ダイエット開発者 中島千鶴子の商品開発が、<br>中小企業庁から認められました。</p>
        </div>
        <div class="new-btn"><a href="http://www.tougan-diet.com/images/newspaper_mozuku.jpg">
                <p>関連記事はコチラ<i class="fa fa-chevron-circle-right" aria-hidden="true"></i></p>
            </a></div>
    </section>
</div> <!-- スモールコンテンツ終了 -->
<!-- ラジオ／youtube -->
<section class="media">
    <section class="line-top">
        <img src="img/line.jpg" alt="ライン">
    </section>
    <!-- ラジオ -->
    <section class="radio-container">
        <div class="title">
            <p>沖縄・北海道・関西のラジオでトウガンダイエットの魅力を発信中!</p>
            <h3>「えっちゃんとちいちゃんのシブイトーク」</h3>
        </div>
        <div class="info">
            <div class="okinawa">
                <p>ラジオ沖縄:毎週土曜日 17:45〜１8:00</p>
            </div>
            <div class="hokkaido">
                <p>北海道STVラジオ:毎週日曜日 5:30〜</p>
            </div>
            <div class="bunka">
                <p>くにまるジャパン 月一回 出演</p>
            </div>
        </div>
        <div class="content">
            <div class="point-1">
                <h3><i class="fa fa-hand-o-right fa-2x" aria-hidden="true"></i>間違いだらけのダイエット</h3>
                <p>食事制限で減るのは筋肉と水分<br>リバウンドするときは脂肪となって帰ってくる。</p>
            </div>
            <div class="point-2">
                <h3><i class="fa fa-hand-o-right fa-2x" aria-hidden="true"></i>体温アップ健康法</h3>
                <p>低体温は病気の元凶、高体温は健康の源<br>有酸素運動は脂肪を減らし、無酸素運動は筋肉を鍛える。</p>
            </div>
        </div>
    </section>
    <!-- youtube -->
    <section class="youtube-container">
        <div class="youtube-title">
            <h3>えっちゃんとちいちゃんの<br>シブイトーク　録音風景</h3>
        </div>
        <div class="moji">
            <p class="ticker">Youtubeで【冬瓜ダイエット】えっちゃんとちいちゃんのシブイトークを公開中！</p>
        </div>
        <div class="video">
            <iframe width="450" height="253" src="https://www.youtube.com/embed/bHDFE8GIqSA" frameborder="0" allowfullscreen></iframe>
        </div>
        <div class="youtube-btn"><a href="https://www.youtube.com/user/sakurabussan">
                <p>冬瓜ダイエット動画はコチラ<i class="fa fa-chevron-circle-right" aria-hidden="true"></i></p>
            </a></div>
    </section>
    <section class="line-bottom">
        <img src="img/line.jpg" alt="ライン">
    </section>
    <!-- 地図 -->
    <section class="map-wrapper">
        <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3579.950954463532!2d127.66657521502977!3d26.198273683440437!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x34e569b1b3029147%3A0x45bab1456c84d1ee!2z44CSOTAxLTAxNTUgT2tpbmF3YS1rZW4sIE5haGEtc2hpLCBLYW5hZ3VzdWt1LCA1IENob21l4oiSMeKIkjEsIO-8t--9geODk-ODqw!5e0!3m2!1sen!2sjp!4v1472567742820" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
        </div>
        <div class="map-box">
            <div class="map-title">
                <h3>冬瓜ダイエットサロンのご案内</h3>
            </div>
            <p>サロン内には、開発者中島千鶴子の冬瓜ダイエット使用前と使用後の等身大パネルがあります。 そして、初めてお見えになった殆どのお客様は驚かれます。</p>
            <p>理由は簡単、パネルの中島が11年前より今のほうが若く見えるから(笑)</p>
            <p>ぜひ実物をご覧くださいませ。</p>
            <div class="map-address">
                <h4>冬瓜ダイエット サロン</h4>
                <p>〒901-0156<br>
                    沖縄県那覇市金城5丁目1-1<br>Waビル 302号室</p>
            </div>
        </div>
    </section>
    <!-- メルマガ -->
    <section class="mail-magazine"><a href="http://www.tougan-diet.com/SHOP/mailmag.html">
            <p><img src="img/mailmagazine-pc.jpg" class="sp-img" width="100%" height="auto" alt="メルマガ"></p>
        </a></section>
</section><!-- ラジオ／youtube終了 -->
<!-- diet -->
<div class="diet-wrapper">
    <div class="diet-title">
        <p>度重なる偶然により開発され、開発者は17kgのダイエットに成功!!</p>
    </div>
    <div class="diet-img">
        <img src="img/before-after.jpg" alt="ビフォーアフター">
    </div>
    <div class="diet-article">
        <dl>
            <dt>開発のきっかけ<i class="fa fa-chevron-circle-down" aria-hidden="true"></i></dt>
            <dd>
                <p>2004年7月、規格外冬瓜の有効活用についての相談を受けました。
                    翌日、偶然にも、別件で共同開発をしていた、琉球大学Ｈ名誉教授より冬瓜が漢方薬であることを知らされました。</p>
                <p>冬瓜が漢方薬と聞き、インターネットで色々調べていると、「冬瓜を毎日300g～500g食べるとダイエット効果がある」という、某大学薬学部の教授のページを見つけました。</p>
                <p>その頃私は70kgくらいあり、様々なダイエットにチャレンジしては失敗。リバウンドの繰り返しでした。
                    私にはもうダイエットは無理だとあきらめていましたが、「冬瓜を毎日300g～500g食べるとダイエット効果がある」という一節に、もう一度冬瓜ダイエットにチャレンジしてみようと思いました。</p>
            </dd>
            <dt>水分を飛ばし粉末化への乾燥実験開始<i class="fa fa-chevron-circle-down" aria-hidden="true"></i></dt>
            <dd>
                <p>食事で毎日冬瓜を食べ続けるのはムリだと思い、水分を飛ばし粉末にして、摂取するときに飛ばした分の水で飲めば良いと考えました。
                    <p>しかしながら、粉末化は相当難しく、低温乾燥機メーカーに冬瓜を送り、乾燥実験をしてもらいましたがうまく行かず、乾燥は無理だとのことでした。</p>
                    <p>どうしてもあきらめきれず、素人ながら、一人で仕事の合間を縫って乾燥実験を始めました。</p>
                    <p>実験に失敗しては捨てることの繰り返し、３ヶ月近くになっても、うまくいきません。
                        「やっぱりダメなのかな？出来ないのかな？」と段々自信がなくなってきました。
                        「でも石の上にも３年というし、私はまだ３ヶ月もやっていない。よし、３ヶ月やってダメならあきらめよう」と思いました。</p>
                    <p>それからしばらくして、その日はなんとなく乾燥してきているように思えましたが、時計の針は午前零時を過ぎ、睡魔には勝てずとうとう帰宅してしまいました。</p>
                    <p>翌朝事務所に行くと、事務員が「乾燥してますよ。」と言うではありませんか。触ってみると本当にサラサラに乾燥しています。あきらめようかと思ってから１週間しか経っていませんでした。</p>
                </p>
            </dd>
            <dt>3ヶ月ほどしたらジーンズが歩くたびにずり落ちるようになり…<i class="fa fa-chevron-circle-down" aria-hidden="true"></i></dt>
            <dd>
                <p>「ヤッター！」すぐに、自分自身で試してみました。朝と夜に３ｇ（生の冬瓜約２００ｇ）ずつコップ一杯の水と共に飲み続けました。</p>
                <p>それから３ヶ月ほどしたらジーンズが歩くたびにずり落ちるようになりました。</p>
                <p>まわりから顔が小さくなったとか痩せたんじゃない？どうやって痩せたの？と声をかけられるようになりました。</p>
                <p>ジーンズも詰めてもらおうとブティックに持っていったら「あら？どうしてそんなに痩せたの？」と言われ嬉しくなり新しいジーンズを買おうと試着したら、ＬＬだったのがＭサイズになっていました。体重を量ってみると９ｋｇ減っていました。</p>
            </dd>
            <dt>冬瓜粉末、事業化への決心<i class="fa fa-chevron-circle-down" aria-hidden="true"></i></dt>
            <dd>
                <p>周りからも欲しいと言われ、お休みの日は殆ど冬瓜作業でした。</p>
                <p>あまりにも評判がいいので、小型の乾燥装置を開発してもらって乾燥冬瓜を製造し、皆様にお試しいただきながら、コツコツと研究を進め、そのうち事業化を考えるようになりましたが、３つの大きな壁にぶち当たりました。</p>
                <p>１．客観的データの不足<br>
                    ２．商品となる乾燥冬瓜の収率が、1つの冬瓜（約3kg）から１．３％、わずか40gだけしかない<br>
                    ３．大型の乾燥装置の開発と装置の製造にかかる費用が８，０００万円</p>
                <p>とても超えることなど出来ない大きな壁の前で「ダメだ、絶対ムリ。」とあきらめました。２００６年８月のことです。</p>
                <p>それから３ヶ月後、１１月の或る日、知り合いの専務様から１本の電話がかかってきました。「ありがとう。」と言って電話の向こうで泣いていらっしゃるのです。私はビックリして「どうしたんですか？何かあったのですか？」と尋ねると、「ありがとう、このご恩は決して忘れません。いつか必ずご恩返しさせていただきます。」とおっしゃるのです。お嬢様からも明るい声でお礼の電話をいただきました。</p>
                <p>以前から、この専務様には大変お世話になっていたので「お嬢様にどうぞ」とさし上げていたのです。こんなにも喜んでいただいて、私の研究がお役にたつなんて信じられないくらい嬉しく感動しました。</p>
                <p>私は、この１本の電話で事業化を決意しました。</p>
                <p>「私がやらなければ誰もやらない、私しかやる人はいないんだ。」</p>
                <p>それから、どうしたら事業化できるかを考えました。</p>
                <p>涙ながらのお礼の電話を頂いたことを機械メーカーの社長に話しました。私がやらなければ誰もやらない、私しかやる人はいないと言うことを訴えました。
                    九州男児の社長は、「よしわかった。機械のことは俺に任せろ。」と言ってくれて、それから３年後の２００８年１２月に、念願の大型乾燥装置の開発に成功しました。</p>
                <p><span class="font11">冬瓜は、９８％以上が水分なので、収率がわずか１．６％しかないという事は、相当な希少価値があることに気が付きました。</span></p>
            </dd>
            <dt>多くの方々に支えられて<i class="fa fa-chevron-circle-down" aria-hidden="true"></i></dt>
            <dd>
                <div class="diet-newspaper"><a href="img/lequio_newspaper.jpg" class="swipe" rel="group1"><img src="img/lequio_newspaper_s.jpg" alt="" /></a> </div>
                <div class="diet-comment">
                    <p>そして、２００７年の３月に友達が補助金の相談に来ました。私の事務所は、沖縄産業支援センターの５階にあり、すぐ下の４階に沖縄県産業振興公社があります。その公社の補助金にトライしたいということでした。</p>
                    <p>友達が来たのは夕方の６時、色々お話を聞いていたら７時３０分くらいになっていました。公社にはもう誰もいないかもしれないけど、と思いながら、友達を事務所に待たせ、公社に行ってみると、偶然にも補助金の担当者の方がまだいて、友達の話をするとそれは補助金の対象ではないと言われました。帰ろうとすると、「あなたはなぜ人のことばかりするの？あなたがやっている冬瓜の研究こそ補助金がでるのですよ。」と教えられました。</p>
                    <p>大学や病院、管理法人もスグにＯＫしてくれて、無事に補助金１０００万円の申請書を出すことが出来ました。</p>
                    <p>琉球大学ではラットを使った検証と病院では人による評価により、懸念事項であった客観的データも得ることが出来ます。</p>
                    <p>偶然ですが、以前応募していた「フジサンケイ女性事業家支援プロジェクト」の最終選考に残ったということで、琉球新報の副読誌「週刊レキオ」から、取材をうけていたのが、「冬瓜で肥満対策」と言うタイトルで１面と３面に大きく掲載されたものが、補助金のプレゼンテーションの日に琉球新報と共に配達されたのです。</p>
                    <p>プレゼン会場に入ったら、審査員の先生方にも記事のコピーが配布されており、ラッキーにも補助金をいただくことができました。</p>
                    <p>研究には、モニターが必要だったのですが、「週刊レキオ」のおかげで、モニターもすぐに集まり「週刊レキオ」社には、心から感謝しています。</p>
                </div>
            </dd>
        </dl>
    </div>
</div>
<!-- 購入 -->
<section class="order-container" id="order-container">
    <div id="order-info">
        <div class="order-title">
            <img src="img/order-pc.jpg" class="sp-img" width="100%" height="auto" alt="購入">
            <h3>沖縄プレゼンツ!冬瓜に秘められた驚異のダイエットパワーを実感してください!</h3>
        </div>
    </div>
    <!-- 商品DBからの表示 -->
    <?php foreach ($product_list as $item) : ?>
        <div class="order-option">
            <div class="order-option-title">
                <h2><i class="fa fa-thumb-tack fa-2x" aria-hidden="true"></i><?=h($item['description'])?></h2>
            </div>
        </div>
        <div class="option">
            <div class="option-img">
                <img src="<?=MAIN_PRODUCT_IMAGE_PATH . (isset($item['img']) ? h($item['img']) : NO_IMAGE_FILE)?>" alt="商品">
            </div>
            <div class="order-text">
                <p><?=h($item['sub_name'])?></p>
                <h2><?=h($item['name'])?></h2>
                <h3>《<?=h($item['day'])?>日分》<span class="font15"><?=h(number_format($item['price']))?>円</span></h3>
                <h4>総額1万円以上は送料無料!</h4>
                <form method="post" action="" target="_top">
                    <input type="hidden" name="product_id" value="<?=h($item['id'])?>">
                    <select name="num">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    <input type="submit" name="cart" value="カートへ">
                </form>
                <br />
                <div class="order-payment-detail">
                    <div class="order-payment">
                        <h4>お支払い方法</h4>
                        <?php $payment = $product->getSelectedPaymentOfProduct($item['id']); ?>
                        <p>
                            <?php foreach ($payment as $val) : ?>
                                <i class="fa <?=h($val['class'])?>" aria-hidden="true"></i><?=h($val['name'])?><br>
                            <?php endforeach; ?>
                        </p>
                    </div>
                    <div class="jumpto_detail">
                        <a class="jump" href="#detail-img">
                            <p>商品詳細はこちら<i class="fa fa-chevron-circle-right" aria-hidden="true"></i></p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<!-- 商品DBからの表示終わり -->
</section>
<!-- 商品の詳細 -->
<section class="product-info">
    <div class="info-title">
        <img src="img/detail-pc.jpg" class="sp-img" width="100%" height="auto" alt="詳細"><a id="detail-img"></a>
    </div>
</section>
<section id="detail">
    <div class="product-detail">
        <h3>商品説明</h3>
        <p>冬瓜は、ウリ科の植物で原産地はジャワと言われ、日本には中国から朝鮮半島を経て４世紀頃伝わってきました。最近、この冬瓜が低カロリーのダイエット食材として注目を集めています。特殊技術により、冬瓜の水分を除去して摂取しやすくしました。たんぱく質、食物繊維、カルシウム、カリウム、ビタミンＢ1、ビタミンＢ2、ビタミンＣ、鉄分等を含有し、ダイエットや健康維持の一助にご利用いただけます。<span class="font12">保存料等の添加物は一切使用しておりませんので、安心してお召し上がり下さい。</span></p>
    </div>
    <div class="product-detail">
        <h3>お召し上がり方</h3>
        <p>1日の目安は2回程度、1回分約3ｇ（1包）を口に入れ、コップ1杯（200cc）の水またはぬるま湯と共にお召し上がり下さい。冬瓜ダイエットを効果的に行うために、<span class="font12">一日1リットル以上のお水またはお茶を飲まれる事を心がけて下さい。</span></p>
    </div>
    <div class="product-detail">
        <h3>ご注意</h3>
        <ul>
            <li>直射日光を避けて冷暗所に保管して下さい。</li>
            <li>薬を服用あるいは通院中の方は、お医者と相談の上お召し上がり下さい。</li>
            <li>体質や体調により、まれに合わない場合があります。その場合は使用を中止、あるいは量を減らしてください。</li>
            <li>本品は特性上同ロットでも、香り、味、色等に少々ばらつきがありますが、品質に影響はございませんので安心してお召し上がりください。</li>
        </ul>
    </div>
    <div class="product-detail">
        <h3>保存方法</h3>
        <p>開封後はチャックをしっかり閉めて、直射日光を避け冷暗所に保管して下さい。</p>
    </div>
    <div class="product-detail">
        <h3>原材料名</h3>
        <p>冬瓜果実</p>
    </div>
    <div class="product-detail">
        <h3>栄養成分　【100g当たり】</h3>
        <p>熱量262kcal、たんぱく質9.5g、脂質4.2g、糖質15.0g、食物繊維63.1g、ナトリウム21.4mg、カルシウム500mg、カリウム2.46g、リン325mg、鉄8.52mg</p>
        <a class="back-order" href="#order-info">
            <h4>商品詳細へ戻る<i class="fa fa-chevron-circle-up" aria-hidden="true"></i></h4>
        </a>
    </div>
</section>
<!-- フッター -->
<?php require_once('./template/footer.php'); ?>
