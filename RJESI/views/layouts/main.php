<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\widgets\Alert;
//use RJESI\assets\AppAsset;
use RJESI\assets\MintyAsset;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

/*
The directory is not writable by the Web process ....AppAsset
chmod -R 777 /var/www/html/backend/web/assets
*/

MintyAsset::register($this);
//AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
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
                'brandLabel' => 'RJ ER SCREENING',
                'brandUrl' => ['/esistatus/index'],
                'options' => [
                    'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
                ],
            ]);
            $menuItems = [
                ['label' => 'Home', 'url' => ['/esistatus/index']],

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

    <footer class="footer mt-auto py-3 text-muted">

    </footer>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage();
