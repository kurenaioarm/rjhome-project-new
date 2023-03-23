<?php
    use kartik\select2\Select2;
use yii\helpers\Html;
?>

<?php if (isset($Array_zipcode_api) && isset($Length_V2) && isset($Check_Status)) { ?>
    <?php
    //    -------------------------------------------------------------- ตรวจสอบค่าว่าง ------------------------------------------------------------------
    if($Check_Status == ""){
        $disabled = true;
        //    ------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ----------------------------------------------------------------
        echo Select2::widget([
            'name' => 'Zipcode',
            'id' => 'Zipcode',
            'value' => $Array_zipcode_api, // initial value
            'data' => $Array_zipcode_api,
            'disabled' => $disabled,
            'size' => Select2::MEDIUM,
            'options' => [
                'placeholder' => 'Search ...',
            ],
            'pluginOptions' => [
                'width' => $Length_V2,
            ],
        ]);
    }else if ($Check_Status == 1) {
        $disabled = false;
        //    ------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ----------------------------------------------------------------
        echo Select2::widget([
            'name' => 'Zipcode',
            'id' => 'Zipcode',
            'value' => $Array_zipcode_api, // initial value
            'data' => $Array_zipcode_api,
            'disabled' => $disabled,
            'size' => Select2::MEDIUM,
            'options' => [
                'placeholder' => 'Search ...',
            ],
            'pluginOptions' => [
                'width' => $Length_V2,
            ],
        ]);
    }else{ ?>
        <input type="text" class="form-control" id="Zipcode" name="Zipcode" autocomplete="off" maxlength="5" style="width: 250px" placeholder="ระบุ : รหัสไปรษณีย" onkeypress="return CheckDate_Zipcode(event)">
    <?php } ?>

<?php } ?>
