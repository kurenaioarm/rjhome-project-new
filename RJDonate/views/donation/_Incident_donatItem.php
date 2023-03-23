<?php
use kartik\select2\Select2;
use yii\helpers\Html;
?>

<?php if (isset($Array_cu_api)  && isset($Array_itemtype_api) && isset($Array_item_api) && isset($Value_itemtype_api) && isset($Value_item_api) && isset($Length_V2) && isset($Check_Status) && isset($Title_Length) && isset($FontS_rm) && isset($Num_Stack)) { ?>

    <div class="input-group mb-3" id="Data_Donate<?php echo $Num_Stack ?>" >
        <!------------------------------------------------------------------------------------------------------ ตัวอย่าง ข้อมูลขอบริจาคสิ่งของ -------------------------------------------------------------------------------------------------------------------------------------->
        <label id="Input_Item_Type" style="display:table;">
            <div class="input-group-prepend">
                <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ประเภทสิ่งของบริจาค</span>
            </div>
        </label>
        <label class="align-items-center" style="display:table;">
            <div id="Box_Itemtype_On<?php echo $Num_Stack ?>">
                <?php
                echo $this->render('_Incident_Itemtype', [
                    'Array_itemtype_api' => $Array_itemtype_api,
                    'Value_itemtype_api' => $Value_itemtype_api,
                    'Check_Status'=>$Check_Status,
                    'Length_V2' => $Length_V2,
                    'Num_Stack' =>$Num_Stack,
                ]) ;
                ?>
            </div>
        </label>

        <label id="Input_Itemmaster_Type" style="display:table;">
            <div class="input-group-prepend">
                <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ของบริจาค</span>
            </div>
        </label>
        <label class="align-items-center" style="display:table;">
            <div id="Box_Item_On<?php echo $Num_Stack ?>">
                <?php
                echo $this->render('_Incident_Item', [
                    'Array_item_api' => $Array_item_api,
                    'Value_item_api' => $Value_item_api,
                    'Check_Status'=>$Check_Status,
                    'Length_V2' => $Length_V2,
                    'Num_Stack' =>$Num_Stack,
                ]) ;
                ?>
            </div>
        </label>

        <label id="Input_Othername<?php echo $Num_Stack ?>" style="display:none;">
            <div class="input-group-prepend">
                <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ระบุ</span>
            </div>
        </label>
        <label class="align-items-center" id="textInput_Othername<?php echo $Num_Stack ?>" style="display:none;">
            <input type="text" class="form-control border-info" id="Othername_On<?php echo $Num_Stack ?>" name="Othername_On<?php echo $Num_Stack ?>"  placeholder="ระบุ : ของบริจาค" autocomplete=off required  style="width: <?php echo  $Length_V2 ?>">
        </label>


        <div class="input-group mb-3" id="Data_Number">
            <label>
                <div class="input-group-prepend">
                    <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">จำนวน</span>
                </div>
            </label>
            <label class="align-items-center" id style="display:table;">
                <input type="number" class="form-control" min ="1" id="Quantity_On<?php echo $Num_Stack ?>" name="Quantity_On<?php echo $Num_Stack ?>"   placeholder="ระบุ : จำนวน" required style="width: 125px">
            </label>
            <label class="align-items-center" style="display:table;">
                <?php
                echo Select2::widget([
                    'name' => 'Itemcu_On'.$Num_Stack,
                    'id' => 'Itemcu_On'.$Num_Stack,
                    'data' => $Array_cu_api,
                    'size' => Select2::MEDIUM,
                    'options' => [
                        'placeholder' => 'ระบุ : หน่วยนับ',
                        'onchange' =>"Check_cu_api(this.value,Itemmaster_On$Num_Stack.value)",
                        'required' => true,//ไม่ให้ว่าง
                    ],
                    'pluginOptions' => [
                        'width' => '168px',
                        'allowClear' => true,
                    ],
                ])
                ?>
            </label>

            <label>
                <div class="input-group-prepend">
                    <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">มูลค่า(ต่อหน่วย)</span>
                </div>
            </label>
            <label>
                <input type="number" class="form-control" min ="0.1" step="0.01" id="Price_On<?php echo $Num_Stack ?>" name="Price_On<?php echo $Num_Stack ?>"  placeholder="มูลค่า(ต่อหน่วย)" required style="width: <?php echo  $Length_V2 ?>">
            </label>
            <label>
                <div class="input-group-prepend">
                    <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">บาท</span>
                </div>
            </label>
    </div>

<?php } ?>
