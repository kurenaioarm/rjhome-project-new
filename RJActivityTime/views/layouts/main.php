<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\widgets\Alert;
//use RJActivityTime\assets\AppAsset;
use RJActivityTime\assets\MintyAsset;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;

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
