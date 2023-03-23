<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\widgets\Alert;
//use RJHCR\assets\AppAsset;
use RJHCR\assets\MintyAsset;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;


/*
The directory is not writable by the Web process ....AppAsset
chmod -R 777 /var/www/html/web/RJHCR/assets
*/

MintyAsset::register($this);
//AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
        <!--ICON => https://www.w3schools.com/icons/icons_reference.asp-->
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header>

        <?php
        NavBar::begin([
            'brandLabel' => 'Health Check Report',
            'brandUrl' => ['/healthcheck/index'],
            'options' => [
                'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
            ],
        ]);
        $menuItems = [
            ['label' => 'Home', 'url' => ['/healthcheck/index']],

            ['label' => 'Logout', 'url' => ['/login/login_his']],
            //        ['label' => 'Contact', 'url' => ['/site/contact']],
        ];
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => $menuItems,
        ]);
        NavBar::end();
        ?>

    </header>

    <main role="main" class="flex-shrink-0">
        <div class="p-4 my-4 border" style="width: 100%">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>

            <?= $content ?>
        </div>
    </main>

    <footer class="footer mt-auto py-3 text-muted " style="font-size: 12px;background-color: #151515;">
        <small style="float:left;"> &nbsp;&nbsp;&nbsp; © Copyright © Health Check Report WebApplication</small>
        <small style="float:right;">Designed by จัดทำโดย ศูนย์คอมพิวเตอร์ รพ.ราชวิถี&nbsp;&nbsp;&nbsp; </small>
    </footer>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage();
