<?php
    use kartik\select2\Select2;
?>


<?php if (isset($Array_ampur_api) && isset($Array_changwat_api) && isset($Check_Status) && isset($DONATE_ID) && isset($DISTRICT)) { ?>

    <?php
    //    -------------------------------------------------------------- ตรวจสอบค่าว่าง ------------------------------------------------------------------
    if($Check_Status == ""){
        $disabled = false;
    }else {
        $disabled = false;
    }
    //    ------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ----------------------------------------------------------------
    echo Select2::widget([
        'name' => 'Ampur'.$DONATE_ID,
        'id' => 'Ampur'.$DONATE_ID,
        'value' => $DISTRICT,
        'data' => $Array_ampur_api,
        'disabled' => $disabled,
        'size' => Select2::MEDIUM,
        'options' => [
            'placeholder' => 'Search ...',
            'onchange' =>"Check_tumbon(this.value,$Array_changwat_api,$DONATE_ID);",
//            'required' => true ,
        ],
        'pluginOptions' => [
            'width' => '250px',
        ],
    ]);
    ?>
<?php } ?>
