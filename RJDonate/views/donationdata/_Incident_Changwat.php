<?php
    use kartik\select2\Select2;
?>

<?php if (isset($Array_changwat_api) && isset($DONATE_ID) && isset($PROVINCE)) { ?>
    <?php //จังหวัด
    //    ------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ----------------------------------------------------------------
    echo Select2::widget([
        'name' => 'Changwat'.$DONATE_ID,
        'id' => 'Changwat'.$DONATE_ID,
        'value' => $PROVINCE,
        'data' => $Array_changwat_api,
        'size' => Select2::MEDIUM,
        'options' => [
            'placeholder' => 'Search ...',
            'onchange' =>"Check_ampur(this.value,$DONATE_ID);",
//            'required' => true ,
        ],
        'pluginOptions' => [
            'width' => '250px',
            'allowClear' => true,

        ],
    ]);
    ?>
<?php } ?>
