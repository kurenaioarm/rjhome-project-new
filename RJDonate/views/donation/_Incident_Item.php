<?php
    use kartik\select2\Select2;
use yii\helpers\Html;
?>

<?php if (isset($Array_item_api) && isset($Value_item_api) && isset($Length_V2) && isset($Num_Stack) && isset($Check_Status)) { ?>
    <?php
    //    --------------------------------------------------------------------------- ตรวจสอบค่าว่าง -------------------------------------------------------------------------------
    if($Check_Status == ""){
        $disabled = false;
        $Data_Value2 = null;
    }else {
        $disabled = false;
        $Data_Value2 = $Value_item_api;
    }
    //    ------------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ของบริจาค -----------------------------------------------------------------------
    echo Select2::widget([
        'name' => 'Itemmaster_On'.$Num_Stack,
        'id' => 'Itemmaster_On'.$Num_Stack,
        'value' => $Data_Value2, // initial value
        'data' => $Array_item_api,
        'size' => Select2::MEDIUM,
        'options' => [
            'placeholder' => 'ของบริจาค',
            'onchange' =>"Check_Itemtype(this.value,$Num_Stack)",
            'required' => true,//ไม่ให้ว่าง
        ],
        'pluginOptions' => [
            'width' => $Length_V2,
        ],
    ]);
    ?>
<?php } ?>
