<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\widgets\Alert;
//use RJDonate\assets\AppAsset;
use RJDonate\assets\MintyAsset;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;


/*
The directory is not writable by the Web process ....AppAsset
chmod -R 777 /var/www/html/web/RJDonate/assets
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
//            'brandLabel' => 'บันทึกรายการ ขอบริจาคสิ่งของ',
//            'brandUrl' => ['/donation/index'],
            'options' => [
                'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
            ],
        ]);

        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');

//                *** การแต่ง class
//                https://getbootstrap.com/docs/5.0/utilities/position/          #position
//                https://getbootstrap.com/docs/4.0/utilities/text/                 #Text
// ------------------------------------------------------------------------------------------------------------
//                *** ตัวอย่างดรอปดาวน์ MENU
//                ['label' => 'Products', 'url' => ['product/index'], 'items' => [
//                    ['label' => 'New Arrivals', 'url' => ['donation/index', 'tag' => 'new']],
//                    ['label' => 'Most Popular', 'url' => ['donation/index', 'tag' => 'popular']],
//                ]],

        if($Access_Token['admin']->json_data === []){
            $menuItems = [
                ['label' => ' แสดงความจำนงขอบริจาคสิ่งของ ', 'url' => ['/donation/index'],
                    'options' => [
                        'style'=>'font-size: 17px;',
                    ]],
                ['label' => 'Logout', 'url' => ['/login/login_his'],
                    'options' => [
                        'class' => 'font-weight-bold',
                        'style'=>'font-size: 17px;position: absolute;right: 100px;',
                    ]],
            ];
        }else{
            $menuItems = [
                ['label' => 'แสดงความจำนงขอบริจาคสิ่งของ' , 'url' => ['/donation/index'],
                    'options' => [
                        'style'=>'font-size: 17px;',
                    ]],
                ['label' => 'รายการที่ขอบริจาค', 'url' => ['/donationdata/index'],'visible' => $Access_Token['admin']->json_data[0]->TYPE_ID === "1" || $Access_Token['admin']->json_data[0]->TYPE_ID === "2",
                    'options' => [
                        'style'=>'font-size: 17px;',
                    ]],
                ['label' => 'จัดการผู้ใช้งาน |', 'url' => ['/donationdata/set_up_admin'],'visible' => $Access_Token['admin']->json_data[0]->TYPE_ID === "1",
                    'options' => [
                        'class' => 'font-weight-bold',
                        'style'=>'font-size: 17px;position: absolute;right: 165px;',
                    ]],
                ['label' => 'Logout', 'url' => ['/login/login_his'],
                    'options' => [
                        'class' => 'font-weight-bold',
                        'style'=>'font-size: 17px;position: absolute;right: 100px;',
                    ]],
            ];
        }

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
