<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\widgets\Alert;
use RJESI\assets\LoginAsset;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

/*
The directory is not writable by the Web process ....AppAsset
chmod -R 777 /var/www/html/backend/web/assets
*/
LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Login Template</title>

        <link rel="stylesheet" href="<?=\yii\helpers\Url::to('@web/../themes_assets/login-template-0/assets/css/materialdesignicons.min.css'); ?>">
        <link rel="stylesheet" href="<?=\yii\helpers\Url::to('@web/../themes_assets/login-template-0/assets/css/bootstrap.min.css'); ?>">
        <link rel="stylesheet" href="<?=\yii\helpers\Url::to('@web/../themes_assets/login-template-0/assets/css/login.css'); ?>">

        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>

    <body>
    <main>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 login-section-wrapper">
                    <div class="login-wrapper my-auto">
                        <?= $content ?>
                    </div>
                </div>
                <div class="col-sm-9 px-0 d-none d-sm-block">
                    <img src="https://rjhome.rajavithi.go.th/themes_assets/login-template-0/assets/images/login-ESI.png" alt="login image" class="login-img">
                </div>
            </div>
        </div>
    </main>
    </body>
    </html>
<?php $this->endPage();
