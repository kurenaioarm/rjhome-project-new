<?php
    use kartik\select2\Select2;
use yii\helpers\Html;
?>

<?php if (isset($Array_item_api) && isset($Length_V2)) { ?>
    <?php
    //    ------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ----------------------------------------------------------------
    echo Select2::widget([
        'name' => 'Itemmaster',
        'id' => 'Itemmaster',
        'data' => $Array_item_api,
        'size' => Select2::MEDIUM,
        'options' => [
            'placeholder' => 'ของบริจาค',
            'onchange' =>"Check_Item(this.value)",
        ],
        'pluginOptions' => [
            'width' => $Length_V2,
            'allowClear' => true,
        ],
    ]);
    ?>
<?php } ?>
