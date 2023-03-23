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

        <link rel="stylesheet" href="<?=\yii\helpers\Url::to('@web/../themes_assets/login-template-1/assets/css/materialdesignicons.min.css'); ?>">
        <link rel="stylesheet" href="<?=\yii\helpers\Url::to('@web/../themes_assets/login-template-1/assets/css/bootstrap.min.css'); ?>">
        <link rel="stylesheet" href="<?=\yii\helpers\Url::to('@web/../themes_assets/login-template-1/assets/css/login.css'); ?>">

        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>

    <body>
    <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
        <div class="container-fluid" style="width: 80% ;">
            <div class="card login-card">
                <div class="row no-gutters">
                    <div class="col-md-8">
                        <img src="https://rjhome.rajavithi.go.th/themes_assets/login-template-0/assets/images/HealthCheck-Report.png" alt="login"  style="width: 100% ; height: 100%">
                    </div>
                    <div class="col-md-4">
                        <div class="card-body">
                            <div class="brand-wrapper">
                                <img src="https://rjhome.rajavithi.go.th/themes_assets/login-template-1/assets/images/RJHome.png" alt="logo" class="logo" style="width: 70% ; height: 70%">
                            </div>
                            <?= $content ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    </body>
    </html>
<?php $this->endPage();
