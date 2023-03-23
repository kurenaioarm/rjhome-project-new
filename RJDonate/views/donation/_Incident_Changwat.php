<?php
    use kartik\select2\Select2;
?>

<?php if (isset($Array_changwat_api) && isset($Length_V2) ) { ?>
    <?php //จังหวัด
    //    ------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ----------------------------------------------------------------
    echo Select2::widget([
        'name' => 'Changwat',
        'id' => 'Changwat',
        'value' => "",
        'data' => $Array_changwat_api,
        'size' => Select2::MEDIUM,
        'options' => [
            'placeholder' => 'Search ...',
            'onchange' =>"Check_ampur(this.value);",
//            'required' => true ,
        ],
        'pluginOptions' => [
            'width' => $Length_V2,
            'allowClear' => true,

        ],
    ]);
    ?>
<?php } ?>
