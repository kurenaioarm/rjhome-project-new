<?php
use kartik\select2\Select2;
use yii\helpers\Html;
?>

<?php if (isset($Array_itemtype_api) && isset($Value_itemtype_api) && isset($Length_V2) && isset($Check_Status) && isset($Num_Stack)) { ?>
    <?php
    //    --------------------------------------------------------------------------- ตรวจสอบค่าว่าง -------------------------------------------------------------------------------
    if($Check_Status == ""){
        $disabled = false;
        $Data_Value = null;
    }else {
        $disabled = false;
        $Data_Value = $Value_itemtype_api;
    }
    //    ------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ประเภทสิ่งของบริจาค ----------------------------------------------------------------
    echo Select2::widget([
        'name' => 'Itemtype_On'.$Num_Stack,
        'id' => 'Itemtype_On'.$Num_Stack,
        'value' => $Data_Value, // initial value
        'data' => $Array_itemtype_api,
        'size' => Select2::MEDIUM,
        'options' => [
            'placeholder' => 'ประเภทสิ่งของบริจาค',
            'onchange' =>"Check_Item(this.value,$Num_Stack)",
            'required' => true,//ไม่ให้ว่าง
        ],
        'pluginOptions' => [
            'width' => $Length_V2,
            'allowClear' => true,
        ],
    ]);
    ?>
<?php } ?>
