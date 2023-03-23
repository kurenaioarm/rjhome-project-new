<?php
    use kartik\select2\Select2;
?>

<?php if (isset($Array_tumbon_api) && isset($Array_changwat_api) && isset($Array_ampur_api) && isset($Check_Status) && isset($DONATE_ID) && isset($TAMBON)) { ?>
    <?php
    //    -------------------------------------------------------------- ตรวจสอบค่าว่าง ------------------------------------------------------------------
    if($Check_Status == ""){
        $disabled = false;
    }else {
        $disabled = false;
    }
    //    ------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ----------------------------------------------------------------
    echo Select2::widget([
        'name' => 'Tambon'.$DONATE_ID,
        'id' => 'Tambon'.$DONATE_ID,
        'value' => $TAMBON,
        'data' => $Array_tumbon_api,
        'disabled' => $disabled,
        'size' => Select2::MEDIUM,
        'options' => [
            'placeholder' => 'Search ...',
            'onchange' =>"Check_zipcode(this.value,$Array_changwat_api,$Array_ampur_api,$DONATE_ID);",
//            'required' => true ,
        ],
        'pluginOptions' => [
            'width' => '250px',
        ],
    ]);
    ?>
<?php } ?>
