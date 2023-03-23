<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \common\models\LoginForm */

use common\widgets\Alert;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;


$this->title = '';
$this->params['breadcrumbs'][] = $this->title;
//var_dump($model);

?>

<div class="site-login">
    <h1 class="login-title"><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="form-group mb-12">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true , 'placeholder' => 'ชื่อผู้ใช้งาน' , 'maxlength' => '13' , 'autocomplete' => 'off', 'onkeypress'=>'return MyKeyUsername(event);' ]) ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'รหัสผ่าน','id'=>'txtpassword','onkeypress'=>'return MyKeyPassword(event);']) ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <?= $form->field($model, 'Showpassword')->checkbox(['checked' => false , 'onchange'=>"mouseDownchangepasswordtotext(this,txtpassword);"]) ?>

            <div class="">
                <?= Html::submitButton('Login', ['class' => 'btn btn-block login-btn', 'name' => 'login-button','']) ?>
            </div>
            <!---------Alert----------->
            <?= Alert::widget() ?>
            <!------------------------->
            <?php ActiveForm::end(); ?>
            <a href="#!" class="forgot-password-link"></a>
            <p class="login-wrapper-footer-text"></p>
        </div>
    </div>
</div>



<script type="text/javascript">

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
        var vchar = String.fromCharCode(event.keyCode);
//        if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
        if ((vchar < '0' || vchar > '9')) return false;
        e.onKeyPress = vchar;
        //===============================================================================================
        var keynum;

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