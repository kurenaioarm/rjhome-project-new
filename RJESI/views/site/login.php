<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="site-login">
    <h1 class="login-title"><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="form-group mb-12">
            <?php $form = ActiveForm::begin(); ?>


            <?= $form->field($model, 'username')->textInput(['autofocus' => true , 'placeholder' => 'ชื่อผู้ใช้งาน']) ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'รหัสผ่าน']) ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>


            <div class="">
                <?= Html::submitButton('Login', ['class' => 'btn btn-block login-btn', 'name' => 'login-button','']) ?>
            </div>

            <?php ActiveForm::end(); ?>
            <a href="#!" class="forgot-password-link"></a>
            <p class="login-wrapper-footer-text"></p>
        </div>
    </div>
</div>


<script>

</script>