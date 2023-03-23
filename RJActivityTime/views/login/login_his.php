<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \common\models\LoginForm */

use common\widgets\Alert;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
//var_dump($model);

?>

<div class="site-login">
    <div class="">
        <div class="form-group mb-12">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true , 'placeholder' => 'ชื่อผู้ใช้งาน' , 'maxlength' => '13' , 'autocomplete' => 'off', 'onkeypress'=>'return MyKeyUsername(event);' ]) ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'รหัสผ่าน','id'=>'txtpassword','onkeypress'=>'return MyKeyPassword(event);']) ?>

            <!--             $form->field($model, 'rememberMe')->checkbox(['checked' => false]) -->

            <?= $form->field($model, 'Showpassword')->checkbox(['checked' => false , 'onchange'=>"mouseDownchangepasswordtotext(this,txtpassword);"]) ?>

            <div class="">
                <?= Html::submitButton('Login', ['class' => 'btn btn-block login-btn', 'name' => 'login-button','','onClick'=>"replaceContentInContainer('target', 'replace_target')"]) ?>
            </div>

            <!--==============================================================ซ่อนและแสดงรูป============================================================================-->
            <div>
                <span id="target"></span>
            </div>
            <div style="display:none">
                <span id="replace_target"><img src="https://rjhome.rajavithi.go.th/RJActivityTime/images/Loading.gif" style="width: 100%; height: 100%"></span>
            </div>
            <!--======================================================================================================================================================-->

            <!---------Alert----------->
            <?= Alert::widget() ?>
            <?php

            ?>
            <!------------------------->

            <?php ActiveForm::end(); ?>
            <a href="https://rjhome.rajavithi.go.th/RJActivityTime/index.php/event/event_index" class="forgot-password-link"><b style="color: blue">+ <u>ปฏิทินกิจกรรม</u></b></a>
            <p class="login-wrapper-footer-text"></p>
        </div>
    </div>
</div>

<script type="text/javascript">

    function replaceContentInContainer(target, source) {
        //========================================ซ่อนและแสดงรูป===============================================
        document.getElementById(target).innerHTML = document.getElementById(source).innerHTML;
        //===============================================================================================
    }

    function mouseDownchangepasswordtotext(e, textbox) {
        //===============================================================================================
        //แสดง password
        if (e.checked === true) {
            textbox.type = "text"
        }
        else {
            textbox.type = "password"
        }
    }

    /**
     * @return {boolean}
     */
    function MyKeyUsername(e) {
        //===============================================================================================
        //ไม่ให้พิมตัวอักษร
        //var vchar = String.fromCharCode(event.keyCode);
        //if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
        //e.onKeyPress = vchar;
        //===============================================================================================
        var keynum;
//        alert(e);
        if (window.event) { // IE
            keynum = e.keyCode;
        } else if (e.which) { // Netscape/Firefox/Opera
            keynum = e.which;
        }
        //===============================================================================================
        //ทำให้สามารถกด ENTER
        if (keynum === 13) {
            $('#btnlogin').click();
        }
        //===============================================================================================
    }

    function MyKeyPassword(e) {
        var keynum;
//        alert(e);
        if (window.event) { // IE
            keynum = e.keyCode;
        } else if (e.which) { // Netscape/Firefox/Opera
            keynum = e.which;
        }
        //===============================================================================================
        //ทำให้สามารถกด ENTER
        if (keynum === 13) {
            $('#btnlogin').click();
        }
        //===============================================================================================
    }
</script>