<?php
require_once('./admin/system/library.php');
confirmAuthUser();
//トークン生成
$_SESSION['token'] = getToken();
$order = new Order();
$user = new User();

//カート内の商品を取得、入っていない場合はcart.phpに遷移
if (!$cart_in_product = $order->getProductInCart($_SESSION['user_id'])) {
    header('Location: cart.php');
    exit;
}

//ユーザー情報の取得
if (!$user_info = $user->getUserInfo($_SESSION['user_id'])) {
    header('Location: error.php?message=system');
    exit;
}

//送付先の設定
$delivery_address = $_POST + $user_info;
//カート内商品の支払い方法を絞り込みして取得
if (($payment = $order->getPaymentInfo($cart_in_product)) === false) {
    header('Location: error.php?message=system');
    exit;
}

//カート内商品に選択できる支払い方法がないのに、この画面へ遷移してきた場合はcart.phpに遷移
if (empty($payment)) {
    header('Location: cart.php');
    exit;
}

//小計と合計数の初期値
$num_total = 0;
$sub_total = 0;

//都道府県コードと名前の取得
$pref = getPref();
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php'); ?>
<div class="cart-container">
    <div class="cart-edit">
        <form id="cart-edit" name="cart" action="cart_conf.php" method="post">
            <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
            <h2>確認</h2>
            <div class="cart-edit-item">
                <table>
                    <tr>
                        <th>商品画像</th>
                        <th>商品名</th>
                        <th>個数</th>
                        <th>単価</th>
                        <th>販売価格</th>
                    </tr>
                    <?php foreach ($cart_in_product as $product) : ?>
                        <tr>
                            <td>
                                <img src="<?=MAIN_PRODUCT_IMAGE_PATH . (isset($product['img']) ? h($product['img']) : NO_IMAGE_FILE)?>">
                            </td>
                            <td><?=h($product['name'])?></td>
                            <td>
                                <?=h($product['num'])?>
                                <?php $num_total += $product['num']; ?>
                            </td>
                            <td><?=h(number_format($product['price']))?>円</td>
                            <?php $price = $product['price'] * $product['num']; ?>
                            <?php $sub_total += $price; ?>
                            <td><?=h(number_format($price))?>円</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="cart-left">小計</td>
                        <td><?=$num_total?></td>
                        <td></td>
                        <td><?=number_format($sub_total)?>円</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="cart-left">送料</td>
                        <?php $shipping_price = $sub_total < 10000 ? 1000 : 0; ?>
                        <td><?=number_format($shipping_price)?>円</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="cart-left">総額</td>
                        <td><?=number_format($sub_total + $shipping_price)?>円</td>
                    </tr>
                </table>
            </div>
            <h2>送付先情報<span class="shipping-change-message">※変更しないを選択した場合、送り先は請求先と同じになります</span></h2>
            <div class="cart-edit-shipping">
                <div class="shipping-change">
                    <label><input id="shipping-form-open" type="radio" name="shipping" value="1" onchange="change(this.value)"<?=isset($_POST['shipping']) && $_POST['shipping'] == 1 ? ' checked' : ''?>>変更する</label>
                    <label><input id="shipping-form-hidden" type="radio" name="shipping" value="0" onchange="noChange(this.value)"<?=empty($_POST['shipping']) || $_POST['shipping'] == 0 ? ' checked' : ''?>>変更しない</label>
                    <!-- javascriptに渡すためのhidden属性 -->
                    <input type="hidden" id="hidden-value" value="<?=isset($_POST['shipping']) ? $_POST['shipping'] : 0?>">
                </div>
                <div class="new-shipping" id="new-shipping">
                    <table>
                        <tr class="borderless">
                            <td colspan="2" class="space"></td>
                            <td colspan="2">
                                <button type="button" class="search-address" onclick="searchAddress()">郵便番号から住所を検索</button>
                                <span class="postal-code-error" id="postal-code-error">※下記の郵便番号からは住所を取得できませんでした</span>
                            </td>
                        </tr>
                        <tr>
                            <th>郵便番号</th>
                            <td class="small">半角</td>
                            <td>
                                <input type="text" class="edit-postal" id="postal-code1" onchange="shippingValidate()" name="postal_code1" value="<?=h($delivery_address['postal_code1'])?>">
                                <span>-</span>
                                <input type="text" class="edit-postal" id="postal-code2" onchange="shippingValidate('postal-code2', '郵便番号', 'postal-code-validate')" name="postal_code2" value="<?=h($delivery_address['postal_code2'])?>">
                                <span class="shipping-error" id="postal-code-validate"><?=isset($_SESSION['error']['postal_code']) ? $_SESSION['error']['postal_code'] : ''?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>住所</th>
                            <td colspan="2">
                                <table class="address borderless intable">
                                    <tr>
                                        <td class="small">都道府県</td>
                                        <td>
                                            <select name="pref" id="edit-pref" onchange="shippingValidate()">
                                                <?php foreach ($pref as $key => $value) : ?>
                                                    <option value="<?=$key?>" <?=$key == $delivery_address['pref'] ? 'selected' : ''?>><?=$value?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="shipping-error" id="pref-validate"><?=isset($_SESSION['error']['pref']) ? $_SESSION['error']['pref'] : ''?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="small">市区町村</td>
                                        <td>
                                            <input type="text" class="edit-city" id="edit-city" onchange="shippingValidate()" name="city" value="<?=h($delivery_address['city'])?>">
                                            <span class="shipping-error" id="city-validate"><?=isset($_SESSION['error']['city']) ? $_SESSION['error']['city'] : ''?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="small">
                                            <li>町名</li>
                                            <li>番地</li>
                                        </td>
                                        <td>
                                            <input type="text" class="edit-address" id="edit-address" onchange="shippingValidate()" name="address" value="<?=h($delivery_address['address'])?>">
                                            <span class="shipping-error" id="address-validate"><?=isset($_SESSION['error']['address']) ? $_SESSION['error']['address'] : ''?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="small">
                                            <li>建物名</li>
                                            <li>部屋番号</li>
                                        </td>
                                        <td>
                                            <input type="text" id="edit-other" class="edit-other" onchange="shippingValidate()" name="other" value="<?=h($delivery_address['other'])?>">
                                            <span class="shipping-error" id="other-validate"><?=isset($_SESSION['error']['other']) ? $_SESSION['error']['other'] : ''?></span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th>電話番号</th>
                            <td class="small">半角</td>
                            <td>
                                <input type="text" id="edit-tel1" onchange="shippingValidate()" class="edit-tel" name="tel1" value="<?=h($delivery_address['tel1'])?>">
                                <span>-</span>
                                <input type="text" id="edit-tel2" onchange="shippingValidate()" class="edit-tel" name="tel2" value="<?=h($delivery_address['tel2'])?>">
                                <span>-</span>
                                <input type="text" id="edit-tel3" onchange="shippingValidate()" class="edit-tel" name="tel3" value="<?=h($delivery_address['tel3'])?>">
                                <span class="shipping-error" id="tel-validate"><?=isset($_SESSION['error']['tel']) ? $_SESSION['error']['tel'] : ''?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>お名前</th>
                            <td colspan="2">
                                <table class="borderless intable">
                                    <tr>
                                        <td class="small">フリガナ</td>
                                        <td><input type="text" id="edit-name-kana" onchange="shippingValidate()" name="name_kana" value="<?=h($delivery_address['name_kana'])?>"><span class="shipping-error" id="name-kana-validate"><?=isset($_SESSION['error']['name_kana']) ? $_SESSION['error']['name_kana'] : ''?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="small">氏名</td>
                                        <td><input type="text" id="edit-name" onchange="shippingValidate()" name="name" value="<?=h($delivery_address['name'])?>"><span class="shipping-error" id="name-validate"><?=isset($_SESSION['error']['name']) ? $_SESSION['error']['name'] : ''?></span></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <h2>請求先情報</h2>
            <div class="cart-edit-bill">
                <table>
                    <tr>
                        <th>郵便番号</th>
                        <td><?=h($user_info['postal_code1'])?>-<?=h($user_info['postal_code2'])?></td>
                    </tr>
                    <tr>
                        <th>住所</th>
                        <td><?=getPref($user_info['pref'])?> <?=h($user_info['city'])?> <?=h($user_info['address'])?> <?=h($user_info['other'])?></td>
                    </tr>
                    <tr>
                        <th>電話番号</th>
                        <td><?=h($user_info['tel1'])?>-<?=h($user_info['tel2'])?>-<?=h($user_info['tel3'])?></td>
                    </tr>
                    <tr>
                        <th>メールアドレス</th>
                        <td><?=h($user_info['mail'])?></td>
                    </tr>
                    <tr>
                        <th>お名前</th>
                        <td>
                            <ul>
                                <li><?=h($user_info['name_kana'])?></li>
                                <li><?=h($user_info['name'])?></li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
            <h2>支払い方法</h2>
            <div class="cart-edit-payment">
                <p><span class="shipping-error" id="payment-validate"><?=isset($_SESSION['error']['payment_id']) ? $_SESSION['error']['payment_id'] : ''?></span></p>
                <table>
                    <tr>
                        <th>支払い方法</th>
                        <td>
                            <?php if (count($payment) == 1) : ?>
                                <?php $payment = array_values($payment); ?>
                                <input type="hidden" id="selected-payment<?=$payment[0]['id']?>" name="payment_id" value="<?=$payment[0]['id']?>" checked>
                                <?=$payment[0]['name']?>
                            <?php else : ?>
                                <?php foreach ($payment as $val) : ?>
                                    <label><input type="radio" id="selected-payment<?=$val['id']?>" onclick="paymentChecked()" name="payment_id" value="<?=$val['id']?>"<?=isset($_POST['payment_id']) && $val['id'] == $_POST['payment_id'] ? ' checked' :''?>><?=$val['name']?></label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="cart-edit-submit">
                <p class="submit-error"><span id="submit-error-message"></span></p>
                <input class="conf" type="submit" id="edit-submit" name="conf" value="確認する">
                <input type="submit" formaction="cart.php" value="カートに戻る">
            </div>
        </form>
    </div>
</div>
<script>
    'use strict'
    //初回訪問時は送付先のフォームを非表示に設定
    let newShipping = document.getElementById("new-shipping");
    newShipping.style.display = "none";
    //確認画面から戻ってきた時に送付先を「変更する」に選択している場合は最初から送付先フォームを表示しておく
    let shipping = document.getElementById("hidden-value").value;
    if (shipping == 1) {
        newShipping.style.display = "block";
    }
    //送付先の変更するボタンが押されたときにフォームを表示、
    function change(value) {
        let formVisible = document.getElementById("shipping-form-open").checked;
        if (formVisible == true) {
            newShipping.style.display = "block";
            shippingValidate();
        }
    }
    //送付先の変更しないボタンが押されたときにフォームを非表示、バリデーションエラー時の挙動をキャンセル
    function noChange(value) {
        let formHidden = document.getElementById("shipping-form-hidden").checked;
        if (formHidden == true) {
            newShipping.style.display = "none";
            document.getElementById('edit-submit').disabled = false;
            document.getElementById('edit-submit').classList.remove('submit-error');
            document.getElementById('submit-error-message').textContent = '';
        }
    }

    ///郵便番号から住所を取得できなった場合のエラー文は訪問時はhidden
    document.getElementById("postal-code-error").style.visibility = "hidden";
    //郵便番号から住所を取得し送付先フォームへ
    let searchAddress = function() {
        document.getElementById("postal-code-error").style.visibility = "hidden";
        let postalCode1 = document.getElementById("postal-code1").value;
        let postalCode2 = document.getElementById("postal-code2").value;
        let _zipcloudAPI = document.body.appendChild(document.createElement("script"));
        _zipcloudAPI.src = "https://zipcloud.ibsnet.co.jp/api/search?zipcode=" + postalCode1 + postalCode2 + "&callback=getAddNameByZipcloudAPI";
        document.body.removeChild(_zipcloudAPI);
    };
    let getAddNameByZipcloudAPI = function(getAdd) {
        if (getAdd.status == 200 && getAdd.results != null) {
            let editPref = getAdd.results[0].prefcode;
            document.getElementById("edit-city").value = getAdd.results[0].address2;
            document.getElementById("edit-address").value = getAdd.results[0].address3;
            document.getElementById("edit-pref").options[editPref].selected = true;
            document.getElementById("postal-code-error").style.visibility = "hidden";
        } else {
            document.getElementById("postal-code-error").style.visibility = "visible";
        }
        shippingValidate();
    };

    //バリデーションパターン
    const valiPostcode1 = /^\d{3}$/;
    const valiPostcode2 = /^\d{4}$/;
    const valiName = /^[^ 　].{0,15}$/;
    const valiNameKana = /^[ァ-ヶ][ァ-ヶ 　 ー]{0,19}$/;
    const valiCity = /^[^ 　].{0,15}$/;
    const valiAddress = /^[^ 　].{0,100}$/;
    const valiOther = /^.{0,100}$/;
    const valiTel = /^\d{1,5}$/;

    //バリデーション開始、引っかかればエラー文表示し確認ボタンを非活性
    function shippingValidate()
    {
        let postalCode1 = document.getElementById('postal-code1').value;
        let postalCode2 = document.getElementById('postal-code2').value;
        let tel1 = document.getElementById('edit-tel1').value;
        let tel2 = document.getElementById('edit-tel2').value;
        let tel3 = document.getElementById('edit-tel3').value;
        let name = document.getElementById('edit-name').value;
        let nameKana = document.getElementById('edit-name-kana').value;
        let pref = document.getElementById('edit-pref').value;
        let city = document.getElementById('edit-city').value;
        let address = document.getElementById('edit-address').value;
        let other = document.getElementById('edit-other').value;
        let error = true;

        //郵便番号のバリデーション
        if (!postalCode1 || !postalCode2) {
            document.getElementById('postal-code-validate').textContent = '※郵便番号が入力されていません';
            error = false;
        } else if (!(valiPostcode1.test(postalCode1)) || !(valiPostcode2.test(postalCode2))) {
            document.getElementById('postal-code-validate').textContent = '※上3桁、下4桁を半角数字でご入力ください';
            error = false;
        } else {
            document.getElementById('postal-code-validate').textContent = '';
        }
        //電話番号のバリデーション
        if (!tel1 || !tel2 || !tel3) {
            document.getElementById('tel-validate').textContent = '※電話番号が入力されていない箇所があります';
            error = false;
        } else if (!valiTel.test(tel1)) {
            document.getElementById('tel-validate').textContent = '※市外局番は1~5桁の半角数字でご入力ください';
            error = false;
        } else if (!valiTel.test(tel2)) {
            document.getElementById('tel-validate').textContent = '※市内局番は1~5桁の半角数字でご入力ください';
            error = false;
        } else if (!valiTel.test(tel3)) {
            document.getElementById('tel-validate').textContent = '※加入者番号は1~5桁の半角数字でご入力ください';
            error.push(1);
        } else {
            document.getElementById('tel-validate').textContent = '';
        }

        //名前のバリデーション
        if (!name) {
            document.getElementById('name-validate').textContent = '※名前が入力されていません';
            error = false;
        } else if (!(valiName.test(name))) {
            document.getElementById('name-validate').textContent = '※15文字以内でご入力ください';
            error = false;
        } else {
            document.getElementById('name-validate').textContent = '';
        }
        if (!nameKana) {
            document.getElementById('name-kana-validate').textContent = '※フリガナが入力されていません';
            error = false;
        } else if (!(valiNameKana.test(nameKana))) {
            document.getElementById('name-kana-validate').textContent = '※20文字以内のカタカナでご入力ください';
            error = false;
        } else {
            document.getElementById('name-kana-validate').textContent = '';
        }
        //住所のバリデーション
        if (pref == 0) {
            document.getElementById('pref-validate').textContent = '※都道府県が選択されていません';
            error = false;
        } else {
            document.getElementById('pref-validate').textContent = '';
        }
        if (!city) {
            document.getElementById('city-validate').textContent = '※市区町村が入力されていません';
            error = false;
        } else if (!(valiCity.test(city))) {
            document.getElementById('city-validate').textContent = '※15文字以内でご入力ください';
            error = false;
        } else {
            document.getElementById('city-validate').textContent = '';
        }

        if (!address) {
            document.getElementById('address-validate').textContent = '※市区町村が入力されていません';
            error = false;
        } else if (!(valiAddress.test(address))) {
            document.getElementById('address-validate').textContent = '※100文字以内でご入力ください';
            error = false;
        } else {
            document.getElementById('address-validate').textContent = '';
        }

        if (!(valiOther.test(other))) {
            document.getElementById('other-validate').textContent = '※100文字以内でご入力ください';
            error = false;
        } else {
            document.getElementById('other-validate').textContent = '';
        }

        //バリデーションに問題がなければエラーメッセージを消してボタンを活性化
        if (error == true) {
            document.getElementById('edit-submit').disabled = false;
            document.getElementById('edit-submit').classList.remove('submit-error');
            document.getElementById('submit-error-message').textContent = '';
        } else {
            document.getElementById('edit-submit').disabled = true;
            document.getElementById('edit-submit').classList.add('submit-error');
            document.getElementById('submit-error-message').textContent = '※送付先フォームの入力形式が正確でない項目があります';
        }

    }

    //カート内商品に適応した支払い方法情報をJSONで取得
    let payment = <?=json_encode($payment)?>;
    //確認ボタンが選択されたときに
    let editSubmit =document.getElementById('edit-submit');
        editSubmit.addEventListener('click', (e) => {
            e.preventDefault();
            let selected = false;
            //支払い方法のradioボタンにcheckedが入っているかの確認
            Object.keys(payment).forEach(function (value) {
                if (document.getElementById('selected-payment' + payment[value].id).checked) {
                    selected = true;
                }
            });
            //支払い方法にチェックが入ってなければエラー文、入っていればsubmit
            if (selected == true) {
                document.getElementById('payment-validate').textContent = '';
                document.cart.submit();
            } else {
                document.getElementById('submit-error-message').textContent = '※支払い方法が選択されていません';
            }
        });

    //支払い方法を選択した場合は一度エラーメッセージを消す、フォームが表示されている場合は再度バリデーションをかける。
    function paymentChecked()
    {
        document.getElementById('submit-error-message').textContent = '';
        if (newShipping.style.display == 'block') {
            shippingValidate();
        }
    }

</script>
<!-- フッターー -->
<?php require_once('./template/footer.php'); ?>
<?php
unset($_SESSION['error']);