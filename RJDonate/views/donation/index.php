<?php

/* @var $this yii\web\View */
use common\widgets\Alert;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;


$this->title = 'My Yii Application';

?>

<!--     on your view layout file HEAD section -->
<link rel="stylesheet" href="<?=\yii\helpers\Url::to('@web/../RJDonate/css/all.css'); ?>">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">

<!-- on your view layout file HEAD section -->
<script src="<?=\yii\helpers\Url::to('@web/../RJDonate/js/jquery.min.js'); ?>"></script>
<script src="<?=\yii\helpers\Url::to('@web/../RJDonate/js/all.js'); ?>"></script>

<!------------------------------------------------------------------- รอโหลดหน้าเว็บ ---------------------------------------------------------------------------------------->
<style type="text/css">
    /*รอโหลดหน้า*/
    #overlay {
        position: absolute;
        top: 0px;
        left: 0px;
        /*background: #ccc;*/
        width: 100%;
        height: 100%;
        /*opacity: .75;*/
        filter: alpha(opacity=100);
        -moz-opacity: .10;
        z-index: 999;
        background: #fcfdfc url(https://rjhome.rajavithi.go.th/assets/images/Loading/LoadindV5.gif) 50% 50% no-repeat;
    }
    .main-contain{
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
</style>
<div id="overlay"></div>
<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------>

<div class="main-contain">
    <div class="body-content">
        <div class="site-index">

            <div class="row">
                <div class="form-group mb-12">
                    <?php $form = ActiveForm::begin(); ?>
                    <!------------------------------------------------------------------------------------------------------- ตรวจสอบขนาดหน้าจอ PHP ----------------------------------------------------------------------------------------------------------------------->
                    <?php if (isset($_GET['Screen_Size'])) {
                        $Check_Length = $_GET['Screen_Size'];
                        if($Check_Length <= 380){ //iPhone SE
                            $TitleArea_Length = 'width: 290px';
                            $LengthArea = 'width: 290px';
                            $Title_Length = 'width: 290px';
                            $Length = 'width: 290px';
                            $Length_V2 = '290px';
                            $FontS_rm='font-size: 12px';
                        }else {
                            if($Check_Length <= 390){  //iPhone 12 Pro
                                $TitleArea_Length = 'width: 310px';
                                $LengthArea = 'width: 310px';
                                $Title_Length = 'width: 310px';
                                $Length = 'width: 310px';
                                $Length_V2 = '310px';
                                $FontS_rm='font-size: 12px';
                            }else {
                                if ($Check_Length <= 414) {   //iPhone XR
                                    $TitleArea_Length = 'width: 330px';
                                    $LengthArea = 'width: 330px';
                                    $Title_Length = 'width: 330px';
                                    $Length = 'width: 330px';
                                    $Length_V2 = '330px';
                                    $FontS_rm='font-size: 12px';
                                } else {
                                    if ($Check_Length <= 896) { //iPad Air
                                        $TitleArea_Length = 'width: 670px';
                                        $LengthArea = 'width: 670px';
                                        $Title_Length = 'width: 170px';
                                        $Length = 'width: 500px';
                                        $Length_V2 = '500px';
                                        $FontS_rm='font-size: 16px';
                                    } else {
                                        if ($Check_Length <= 1180) { //iPad Air
                                            $TitleArea_Length = 'width: 1050px';
                                            $LengthArea = 'width: 1050px';
                                            $Title_Length = 'width: 160px';
                                            $Length = 'width: 380px';
                                            $Length_V2 = '380px';
                                            $FontS_rm='font-size: 16px';
                                        } else {
                                            $TitleArea_Length = 'width: 890px';
                                            $LengthArea = 'width: 890px';
                                            $Title_Length = 'width: 165px';
                                            $Length = 'width: 290px';
                                            $Length_V2 = '290px';
                                            $FontS_rm='font-size: 16px';
                                        }
                                    }
                                }
                            }
                        } ?>
                    <?php } else {
                        $TitleArea_Length = '';
                        $LengthArea = '';
                        $Length = '';
                        $Title_Length = '';
                        $Length_V2 = '';
                        $FontS_rm = '';
                        ;?>
                        <!------------------------------------------------------------------------------------------------------- เช็คขนาดหน้าจอ script ----------------------------------------------------------------------------------------------------------------->
                        <script>
                            width = screen.width;
//                            window.location.href = "http://rjhome.com/RJDonate/index.php/donation/index?Screen_Size=" + width ; //TEST
                            window.location.href = "https://rjhome.rajavithi.go.th/RJDonate/index.php/donation/index?Screen_Size=" + width ;
                        </script>
                        <!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>
                    <?php } ?>
                    <!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
                    <br>
                    <b style="font-size:22px;color: black">ระบบงาน สำหรับแสดงความจำนงขอบริจาคสิ่งของ</b>

                    <?php if (isset($FontS_rm)&&isset($Current_DateThai)&&isset($Current_Time)&&isset($Access_Token)&&isset($Next_Booknum)) { ?>
                    <?= Alert::widget() ?><br>
                    <div class="card my-3 border-info" >
                        <div class="card-header">
                            <b style="color: black; <?php echo $FontS_rm ?>">ข้อมูลทั่วไป</b>
                        </div>
                        <div class="card-body bg-secondary">
                            <!--'autocomplete'=>'off' = ไม่จำค่าที่เคยกรอกไป -->
                            <br> <br>
                            <div class="input-group mb-3 ">
                                <!----------------------------------------------------------------------------------------------------- ตัวอย่าง วันที่ทำรายการ,เวลาทำรายการ -------------------------------------------------------------------------------------------------------------------------------------------->
                                <label>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">วันที่ทำรายการ</span>
                                    </div>
                                </label>
                                <label>
                                    <?= $form->field($model, 'Report_Date')->textInput(['class'=>'form-control border-info' , 'id' => 'Report_Date' , 'style'=> $Length , 'value'=>$Current_DateThai , 'autocomplete'=>'off' , 'disabled' => true])->label(false) ?>
                                    <?php echo  Html::hiddenInput('Report_Date', $Current_DateThai); ?>
                                </label>
                                <label>
                                    <div class=" input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">เวลาทำรายการ</span>
                                    </div>
                                </label>
                                <label>
                                    <?= $form->field($model, 'Report_Time')->textInput(['class'=>'form-control border-info' , 'id' => 'Report_Time'  , 'style'=> $Length , 'value'=>$Current_Time , 'autocomplete'=>'off' , 'disabled' => true])->label(false) ?>
                                    <?php echo  Html::hiddenInput('Report_Time', $Current_Time); ?>
                                </label>
                                <!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->

                                <!------------------------------------------------------------------------------------------------------ ตัวอย่าง การใส่เงื่อนไขให้ radioList YII2 -------------------------------------------------------------------------------------------------------------------------------------->
                                <label>
                                    <div class=" input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ประเภทบุคคล</span>
                                    </div>
                                </label>
                                <label>
                                    <?= $form->field($model, 'Name_Type')
                                        ->radioList([1=>'บุคคลธรรมดา', 2 => 'นิติบุคคล/บริษัท/มูลนิธิ'],[ 'class' => 'btn btn-light  form-control border-info',
                                            'item' => function($index, $label, $name, $checked, $value){
                                                return "<label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"".$name."\" id=\"".$name."\" value=\"".$value."\" onchange=\"CheckRadio_One(this.value)\" required>"."&nbsp;".$label."</label>";
                                            }
                                        ])->label(false);
                                    ?>
                                </label>
                                <!----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->


                                <div class="input-group mb-3">
                                    <!------------------------------------------------------------------------------------------------------------- ตัวอย่าง ฟอร์มชื่อ-นามสกุล  ------------------------------------------------------------------------------------------------------------------------------------------------>
                                    <label id="Input_My_Pname" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">คำนำหน้าชื่อ</span>
                                        </div>
                                    </label>
                                    <label id="textInput_My_Pname" style="display:none;">
                                        <?php
                                        if (isset($Array_pname_api)) {
                                            echo $form->field($model, 'My_Pname')->widget(Select2::classname(), [
                                                'data' => $Array_pname_api,
                                                'id' => 'My_Pname',
                                                'size' => Select2::MEDIUM,
                                                'disabled' => false,
                                                'options' => [
                                                    'placeholder' => 'Select a state ...',
                                                    'value' => 999, // initial value
                                                ],
                                                'pluginOptions' => [
                                                    'width' => $Length_V2,
                                                    'allowClear' => true
                                                ],
                                            ])->label(false);
                                        }
                                        ?>
                                    </label>
                                    <label id="Input_My_Name" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ชื่อ</span>
                                        </div>
                                    </label>
                                    <label class="align-items-center" id="textInput_My_Name" style="display:none;">
                                        <?= $form->field($model, 'My_Name')->textInput(['class'=>'form-control border-info' , 'id' => 'My_Name' , 'style'=> $Length , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                    </label>
                                    <label  id="Input_My_Lname" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">นามสกุล</span>
                                        </div>
                                    </label>
                                    <label class="align-items-center"  id="textInput_My_Lname" style="display:none;">
                                        <?= $form->field($model, 'My_Lname')->textInput(['class'=>'form-control border-info' , 'id' => 'My_Lname' , 'style'=> $Length , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                    </label>
                                    <label id="Input_Taxpayer_Number" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">เลขผู้เสียภาษี</span>
                                        </div>
                                    </label>
                                    <label id="textInput_Taxpayer_Number" style="display:none;">
                                        <?= $form->field($model, 'Taxpayer_Number')->textInput(['class'=>'form-control' , 'id' => 'Taxpayer_Number' , 'maxlength' => '13'   , 'style'=> $Length ,  'autocomplete'=>'off' , 'onkeypress' =>"return CheckDate_Taxpayer(event)"])->label(false) ?>
                                    </label>
                                    <!----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->

                                    <div class="input-group mb-3 ">
                                        <!------------------------------------------------------------------------------------------------------ ตัวอย่าง การใส่เงื่อนไขให้ radioList YII2 -------------------------------------------------------------------------------------------------------------------------------------->
                                        <label id="Input_Telephone" style="display:none;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">เบอร์โทรศัพท์</span>
                                            </div>
                                        </label>
                                        <label id="textInput_Telephone" style="display:none;">
                                            <?= $form->field($model, 'Telephone')->textInput(['class'=>'form-control' , 'id' => 'Telephone' , 'maxlength' => '10'  , 'style'=> $Length ,  'autocomplete'=>'off' , 'required' => true , 'placeholder'=>'ระบุ : เบอร์โทรศัพท์' , 'onkeypress' =>"return CheckDate_Telephone(event)"])->label(false) ?>
                                        </label>
                                        <label id="Input_Letter_Type" style="display:none;">
                                            <div class=" input-group-prepend">
                                                <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ประเภทการรับหนังสือ</span>
                                            </div>
                                        </label>
                                        <label id="textInput_Letter_Type" style="display:none;width: 743px">
                                            <?= $form->field($model, 'Letter_Type')
                                                ->radioList([1=>'ต้องการหนังสือตอบขอบคุณ', 2 => 'ไม่ต้องการหนังสือตอบขอบคุณ'],[ 'class' => 'btn btn-light form-control border-info',
                                                    'item' => function($index, $label, $name, $checked, $value){
                                                        return "<label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"".$name."\" value=\"".$value."\" onchange=\"CheckRadio_Two(this.value)\">"."&nbsp;".$label."</label>";
                                                    }
                                                ])->label(false);
                                            ?>
                                        </label>
                                        <!----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
                                        <div class="input-group mb-3 ">
                                            <label id="Input_Register_Type" style="display:none;">
                                                <div class=" input-group-prepend">
                                                    <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">การลงทะเบียน</span>
                                                </div>
                                            </label>
                                            <label id="textInput_Register_Type" style="display:none;width: 450px">
                                                <?= $form->field($model, 'Register_Type')
                                                    ->radioList([1=>'ลงทะเบียนครุภัณฑ์', 2 => 'ไม่ลงทะเบียนครุภัณฑ์'],[ 'class' => 'btn btn-light form-control border-info',
                                                        'item' => function($index, $label, $name, $checked, $value){
                                                            return "<label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"".$name."\" value=\"".$value."\">"."&nbsp;".$label."</label>";
                                                        }
                                                    ])->label(false);
                                                ?>
                                            </label>
                                        </div>
                                    </div>

                                    <label id="Input_Letter_Donate_Note" style="display:none;">
                                        <div class="">
                                            <span class="btn btn-info" style="<?php echo $TitleArea_Length ?>;<?php echo $FontS_rm ?>;">หมายเหตุ : บริจาคเนื่องในโอกาส</span>
                                        </div>
                                        <?= $form->field($model, 'Donate_Note')->textArea(['class'=>'' , 'id' => 'Donate_Note' , 'style'=> $TitleArea_Length ,  'autocomplete'=>'off' , 'onchange' =>"CheckDate_ALL()"])->label(false) ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card my-3 border-info"  id="Input_Address" style="display:none;width: 100%;">
                        <div class="card-header">
                            <b style="color: black; <?php echo $FontS_rm ?>">ข้อมูลที่อยู่</b>
                        </div>
                        <div class="card-body bg-secondary"><br>
                            <div class="input-group mb-3">
                                <label id="Input_Address_No"  style="display:none;">
                                    <div class="input-group-prepend" >
                                        <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ที่อยู่ เลขที่</span>
                                    </div>
                                </label>
                                <label class="align-items-center" id="textInput_Address_No"  style="display:none;">
                                    <?= $form->field($model, 'Address_No')->textInput(['class'=>'form-control border-info' , 'id' => 'Address_No' , 'style'=> $Length , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                </label>
                                <label id="Input_Alley"  style="display:none;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ซอย</span>
                                    </div>
                                </label>
                                <label class="align-items-center" id="textInput_Alley"  style="display:none;">
                                    <?= $form->field($model, 'Alley')->textInput(['class'=>'form-control border-info' , 'id' => 'Alley' , 'style'=> $Length , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                </label>
                                <label  id="Input_Road"  style="display:none;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ถนน</span>
                                    </div>
                                </label>
                                <label class="align-items-center"  id="textInput_Road"  style="display:none;">
                                    <?= $form->field($model, 'Road')->textInput(['class'=>'form-control border-info' , 'id' => 'Road' , 'style'=> $Length , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                </label>


                                <div class="input-group mb-3" >
                                    <!------------------------------------------------------------------------------------------------------ ตัวอย่าง พื้นที่ ตำบล/แขวง , อำเภอ/เขต , จังหวัด , รหัสไปรษณีย์ -------------------------------------------------------------------------------------------------------------------------------------->
                                    <label id="Input_Changwat_Type" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">จังหวัด</span>
                                        </div>
                                    </label>
                                    <label class="align-items-center" id="textInput_Changwat_Type" style="display:none;">
                                        <?php
                                        if (isset($Array_changwat_api)) {
                                            echo '<div id="Box_Incident_Changwat">';
                                            echo $this->render('_Incident_Changwat', [
                                                'Array_changwat_api' => $Array_changwat_api,
                                                'model'=> $model,
                                                'Length_V2' => $Length_V2,
                                            ]) ;
                                            echo '</div>';
                                        }
                                        ?>
                                    </label>
                                    <label id="Input_Ampur_Type" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">อำเภอ/เขต</span>
                                        </div>
                                    </label>
                                    <label class="align-items-center" id="textInput_Ampur_Type" style="display:none;">
                                        <?php
                                        if (isset($Array_ampur_api)) {
                                            echo '<div id="Box_Incident_Ampur">';
                                            echo $this->render('_Incident_Ampur', [
                                                'Array_ampur_api' => $Array_ampur_api,
                                                'Array_changwat_api' => "",
                                                'Check_Status'=>"",
                                                'model'=> $model,
                                                'Length_V2' => $Length_V2,
                                            ]) ;
                                            echo '</div>';
                                        }
                                        ?>
                                    </label>
                                    <label  id="Input_Tambon_Type" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">ตำบล/แขวง</span>
                                        </div>
                                    </label>
                                    <label class="align-items-center" id="textInput_Tambon_Type" style="display:none;">
                                        <?php
                                        if (isset($Array_tumbon_api)) {
                                            echo '<div id="Box_Incident_Tumbon">';
                                            echo $this->render('_Incident_Tumbon', [
                                                'Array_changwat_api' => "",
                                                'Array_ampur_api' => "",
                                                'Array_tumbon_api' => $Array_tumbon_api,
                                                'Check_Status'=>"",
                                                'model'=> $model,
                                                'Length_V2' => $Length_V2,
                                            ]) ;
                                            echo '</div>';
                                        }
                                        ?>
                                    </label>
                                    <!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
                                </div>
                                <?php } ?>
                                <div class="input-group mb-3" >
                                    <label id="Input_Zip_Code" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="<?php echo $Title_Length ?>;<?php echo $FontS_rm ?>;">รหัสไปรษณีย์</span>
                                        </div>
                                    </label>
                                    <label id="textInput_Zip_Code" style="display:none;">
                                        <?php
                                        echo '<div id="Box_Incident_Zipcode">';
                                        echo $this->render('_Incident_Zipcode', [
                                            'Array_zipcode_api' => "",
                                            'Check_Status'=>"",
                                            'model'=> $model,
                                            'Length_V2' => $Length_V2,
                                        ]) ;
                                        echo '</div>';
                                        ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card my-3 border-primary"  id="Input_Donations" style="display:none;">
                        <div class="card-header">
                            <b style="color: black; <?php echo $FontS_rm ?>">ข้อมูลขอบริจาคสิ่งของ ดังนี้</b>
                        </div>

                        <div class="card-body bg-secondary">
                            <!--------------------------------------- HTML ที่ใช้ในการส่งค่าและการอ่านค่าแบบซ่อนไปกับฟอร์ม type="hidden" --------------------------------------------------->
                            <input type="hidden" value="1" id="Num_Stack" name="Num_Stack" >
                            <!--------------------------------------------------------------------------------------------------------------------------------------------------------->
                            <!---------------------------------------------------- Button โดยใช้ ID เป็น function jquery  ------------------------------------------------------------------>
                            <label id="Add_on">
                                <!--https://www.w3schools.com/icons/icons_reference.asp => ICON PHP -->
                                <span class="input-group-text btn btn-outline-success btn-sm" id="Increase" style="width: 100px"><b>Add <i class="fas fa-folder-plus" style="font-size:19px;"></i></b></span>
                                <?php echo "&nbsp;&nbsp;&nbsp;" ?>
                                <span class="input-group-text btn btn-outline-danger btn-sm" id="Reduce" style="width: 100px"><b>Remove <i class="fas fa-folder-minus" style="font-size:19px;"></i></b></span>
                            </label>
                            <!--------------------------------------------------------------------------------------------------------------------------------------------------------------->
                            <br>
                            <?php
                            if (isset($Array_itemtype_api) && isset($Array_item_api)&& isset($Array_cu_api)) {
                                echo '<div id="Box_All_Item">';
                                echo $this->render('_Incident_donatItem', [
                                    'Array_cu_api' => $Array_cu_api,
                                    'Array_itemtype_api' => $Array_itemtype_api,
                                    'Array_item_api' => $Array_item_api,
                                    'Value_itemtype_api' => "",
                                    'Value_item_api' => "",
                                    'Check_Status'=>"",
                                    'model'=> $model,
                                    'Length_V2' => $Length_V2,
                                    'Title_Length' => $Title_Length,
                                    'FontS_rm' => $FontS_rm,
                                    'Num_Stack' =>1,
                                ]) ;
                                echo '</div>';
                            }
                            ?>

                        </div>
                    </div>
                </div>
                <!-----------------------------------------------------------------------------------------------Button Submit----------------------------------------------------------------------------------------------------------------------------->
                <br>
                <div class="" id="Submit_Succes"  style="<?php echo $FontS_rm ?>;display:table;width: 100%">
                    <?= Html::submitButton('บันทึกข้อมูล (Save Data)', ['class' => 'btn btn-dark btn-block' , 'id' => 'ConFirm_Disabled', 'name' => 'Check-button', 'value'=>1 ,  'disabled' => true ,  'onClick'=>"SubmitDate(Taxpayer_Number.value,Telephone.value)"]) ?>
                </div>
                <b style="font-size: 14px">*** หมายเหตุ : กรณีไม่สามารถ <u style="color: red;">กดบันทึกข้อมูลได้</u> "ให้ระบุ : หน่วยนับใหม่อีกครั้ง" <u style="color: red;"> หลังจากกรอกข้อมูลให้ครบถ้วน</u></b>
                <br>
                <!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>





<script type="text/javascript">

    $(function(){
        //========================================รอโหลดหน้า===============================================
        $("#overlay").fadeOut();
        $(".main-contain").removeClass("main-contain");
        //===============================================================================================
    });

    $("#Increase").click(function(){//ตัวอย่าง การเรียกใช้ function แบบ jquery
        $( "#ConFirm_Disabled" ).prop( "disabled", true );
        document.getElementById('Submit_Succes').style.display = 'table';
        //            document.getElementById('Num_Stack').value = parseInt( document.getElementById('Num_Stack').value)+1; //ตัวอย่างแบบ javascript
        //            document.getElementById('Num_Stack').value = parseInt($('#Num_Stack').val())+1; //ตัวอย่างแบบผสม javascript และ jquery
        //            Number();//: แปลงออบเจ็คใดๆ ให้เป็นตัวเลข
        //            parseInt();//: แปลง String ให้เป็นตัวเลขจำนวนเต็ม
        //            parseFloat();//: แปลง String ให้เป็นตัวเลขจำนวนเต็มทศนิยม
        $('#Num_Stack').val( parseInt($('#Num_Stack').val())+1); //ตัวอย่างแบบ jquery
        var Num_Stack =  parseInt( document.getElementById('Num_Stack').value);
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['donation/checkitemall']) ?>',
            type: 'POST',
            data: 'Num_Stack='+Num_Stack+'&Itemtype_ID='+""+'&Item_ID='+""+'&Length_V2=<?=$Length_V2?>'+'&Title_Length=<?=$Title_Length?>'+'&FontS_rm=<?=$FontS_rm?>',
            success: function (data) {
                $('#Box_All_Item').append(data);
            }
        });
    });

    $("#Reduce").click(function(){//ตัวอย่าง การเรียกใช้ function แบบ jquery
        $('#Num_Stack').val( parseInt($('#Num_Stack').val())); //ตัวอย่างแบบ jquery
        var Num_Stack =  parseInt( document.getElementById('Num_Stack').value);
        if(Num_Stack > 1){
            $( "#ConFirm_Disabled" ).prop( "disabled", true );
            document.getElementById('Submit_Succes').style.display = 'table';
            $('#Num_Stack').val( parseInt($('#Num_Stack').val())-1); //ตัวอย่างแบบ jquery
            var Num_Stack =  parseInt( document.getElementById('Num_Stack').value);
            var RemoveNum_Stack = Num_Stack+1;
            $("#Data_Donate"+RemoveNum_Stack).remove();
        }else {
            alert("ไม่สามารถ ลบ ได้มากกว่านี้แล้ว");
        }
    });


    function SubmitDate(taxpayer,telephone) {

        if(taxpayer === ""){
            document.getElementById('Taxpayer_Number').value = "0";
        }
        if(taxpayer === "0" && telephone === ""){
            let text = "เบอร์โทรศัพท์ มีผลต่อการติดต่อกลับ \n ยืนยันไม่ระบุ ใช่ หรือ ไม่";
            if (confirm(text) === true) {
                document.getElementById('Telephone').value = "0";
            } else {
                document.getElementById('Telephone').value = "";
            }
        }else {
            document.getElementById('Submit_Succes').style.display = 'none';
        }
    }

    function CheckDate_Quantity(e) {
        //======================================== ไม่ให้พิมตัวอักษร ===========================================
        let vchar = String.fromCharCode(event.keyCode);
//        if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
        if ((vchar < '0' || vchar > '9')) return false;
        e.onKeyPress = vchar;
        //===============================================================================================
    }

    function CheckDate_Price (e){
        let vchar = String.fromCharCode(event.keyCode);
        if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
        e.onKeyPress = vchar;
    }

    function CheckDate_Taxpayer(e){
        //======================================== ไม่ให้พิมตัวอักษร ===========================================
        let vchar = String.fromCharCode(event.keyCode);
//        if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
        if ((vchar < '0' || vchar > '9')) return false;
        e.onKeyPress = vchar;
        //===============================================================================================
    }
    function CheckDate_Telephone(e) {
        //======================================== ไม่ให้พิมตัวอักษร ===========================================
        let vchar = String.fromCharCode(event.keyCode);
//        if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
        if ((vchar < '0' || vchar > '9')) return false;
        e.onKeyPress = vchar;
        //===============================================================================================
    }
    function CheckDate_Zipcode(e) {
        //======================================== ไม่ให้พิมตัวอักษร ===========================================
        let vchar = String.fromCharCode(event.keyCode);
//        if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
        if ((vchar < '0' || vchar > '9')) return false;
        e.onKeyPress = vchar;
        //===============================================================================================
    }


    function  CheckRadio_One(data) {
       if(data === "1"){//บุคคลธรรมดา
           document.getElementById('Input_My_Pname').style.display = 'table';
           document.getElementById('textInput_My_Pname').style.display = 'table';

           document.getElementById('Input_My_Name').style.display = 'table';
           document.getElementById('textInput_My_Name').style.display = 'table';
           document.getElementById('My_Name').placeholder = "ระบุ : ชื่อ";
           document.getElementById('My_Name').value = "";

           document.getElementById('Input_My_Lname').style.display = 'table';
           document.getElementById('textInput_My_Lname').style.display = 'table';
           document.getElementById('My_Lname').placeholder = "ระบุ : นามสกุล";
           document.getElementById('My_Lname').value = "";

           document.getElementById('Input_Taxpayer_Number').style.display = 'none';
           document.getElementById('textInput_Taxpayer_Number').style.display = 'none';
           document.getElementById('Taxpayer_Number').value = "0";

           document.getElementById('Input_Letter_Type').style.display = 'table';
           document.getElementById('textInput_Letter_Type').style.display = 'table';

           document.getElementById('Input_Register_Type').style.display = 'table';
           document.getElementById('textInput_Register_Type').style.display = 'table';

           document.getElementById('Input_Telephone').style.display = 'table';
           document.getElementById('textInput_Telephone').style.display = 'table';

           document.getElementById('Input_Letter_Donate_Note').style.display = 'table';
       }else {//นิติบุคคล/บริษัท/มูลนิธิ
           document.getElementById('Input_My_Pname').style.display = 'table';
           document.getElementById('textInput_My_Pname').style.display = 'table';

           document.getElementById('Input_My_Name').style.display = 'table';
           document.getElementById('textInput_My_Name').style.display = 'table';
           document.getElementById('My_Name').placeholder = "ระบุ : ชื่อบริษัท/นิติบุคล/มูลนิธิ";
           document.getElementById('My_Name').value = "";

           document.getElementById('Input_My_Lname').style.display = 'none';
           document.getElementById('textInput_My_Lname').style.display = 'none';
           document.getElementById('My_Lname').placeholder = "";
           document.getElementById('My_Lname').value = "-";

           document.getElementById('Input_Taxpayer_Number').style.display = 'table';
           document.getElementById('textInput_Taxpayer_Number').style.display = 'table';
           document.getElementById('Taxpayer_Number').placeholder = "ระบุ : เลขผู้เสียภาษี";
           document.getElementById('Taxpayer_Number').value = "";

           document.getElementById('Input_Letter_Type').style.display = 'table';
           document.getElementById('textInput_Letter_Type').style.display = 'table';

           document.getElementById('Input_Register_Type').style.display = 'table';
           document.getElementById('textInput_Register_Type').style.display = 'table';

           document.getElementById('Input_Telephone').style.display = 'table';
           document.getElementById('textInput_Telephone').style.display = 'table';

           document.getElementById('Input_Letter_Donate_Note').style.display = 'table';

       }
    }

    function  CheckRadio_Two(data) {
        if(data === "1"){//ต้องการหนังสือตอบขอบคุณ

            $( "#ConFirm_Disabled" ).prop( "disabled", true );
            document.getElementById('Input_Address').style.display = 'table';
            document.getElementById('Input_Donations').style.display = 'none';

            document.getElementById('Input_Address_No').style.display = 'table';
            document.getElementById('textInput_Address_No').style.display = 'table';

            document.getElementById('Input_Alley').style.display = 'table';
            document.getElementById('textInput_Alley').style.display = 'table';

            document.getElementById('Input_Road').style.display = 'table';
            document.getElementById('textInput_Road').style.display = 'table';

            document.getElementById('Input_Tambon_Type').style.display = 'table';
            document.getElementById('textInput_Tambon_Type').style.display = 'table';

            document.getElementById('Input_Ampur_Type').style.display = 'table';
            document.getElementById('textInput_Ampur_Type').style.display = 'table';

            document.getElementById('Input_Changwat_Type').style.display = 'table';
            document.getElementById('textInput_Changwat_Type').style.display = 'table';

            document.getElementById('Input_Zip_Code').style.display = 'table';
            document.getElementById('textInput_Zip_Code').style.display = 'table';

        }else {//ไม่ต้องการหนังสือตอบขอบคุณ
            $( "#ConFirm_Disabled" ).prop( "disabled", true );
            document.getElementById('Input_Address').style.display = 'none';
            document.getElementById('Input_Donations').style.display = 'table';

            document.getElementById('Input_Address_No').style.display = 'none';
            document.getElementById('textInput_Address_No').style.display = 'none';
            document.getElementById('Address_No').value = " ";

            document.getElementById('Input_Alley').style.display = 'none';
            document.getElementById('textInput_Alley').style.display = 'none';
            document.getElementById('Alley').value = " ";

            document.getElementById('Input_Road').style.display = 'none';
            document.getElementById('textInput_Road').style.display = 'none';
            document.getElementById('Road').value = " ";

            document.getElementById('Input_Tambon_Type').style.display = 'none';
            document.getElementById('textInput_Tambon_Type').style.display = 'none';
            document.getElementById('Tambon').value = " ";

            document.getElementById('Input_Ampur_Type').style.display = 'none';
            document.getElementById('textInput_Ampur_Type').style.display = 'none';
            document.getElementById('Ampur').value = " ";

            document.getElementById('Input_Changwat_Type').style.display = 'none';
            document.getElementById('textInput_Changwat_Type').style.display = 'none';
            document.getElementById('Changwat').value = " ";

            document.getElementById('Input_Zip_Code').style.display = 'none';
            document.getElementById('textInput_Zip_Code').style.display = 'none';
            document.getElementById('Zipcode').value = " ";

        }
    }

    function Check_ampur(data) {//อำเภอทั้งหมด
        if(data === ""){
            $( "#ConFirm_Disabled" ).prop( "disabled", true );
            document.getElementById('Input_Donations').style.display = 'none';
        }
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['donation/checkampur']) ?>',
            type: 'POST',
            data: 'Changwat_ID='+data+'&Length_V2=<?=$Length_V2?>',
            success: function (data) {
                $('#Box_Incident_Ampur').html(data);
            }
        });
    }
    function Check_tumbon(data,changwat) {//ตำบลทั้งหมด
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['donation/checktumbon']) ?>',
            type: 'POST',
            data: 'Ampurstep2_ID='+data+'&Changwatstep2_ID='+changwat+'&Length_V2=<?=$Length_V2?>',
            success: function (data) {
                $('#Box_Incident_Tumbon').html(data);
            }
        });
    }

    function Check_zipcode(data,changwat,ampur){
        if(changwat === ""){
            $( "#ConFirm_Disabled" ).prop( "disabled", true );
        }else {
            $( "#ConFirm_Disabled" ).prop( "disabled", true );
            document.getElementById('Input_Donations').style.display = 'table';
        }
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['donation/checkzipcode']) ?>',
            type: 'POST',
            data: 'Tumbonstep3_ID='+data+'&Changwatstep3_ID='+changwat+'&Ampurstep3_ID='+ampur+'&Length_V2=<?=$Length_V2?>',
            success: function (data) {
                $('#Box_Incident_Zipcode').html(data);
            }
        });
    }

    function Check_Itemtype(data,Num_Stack) {
        let Datecut = data.substring(3);
        if(Datecut === "99"){
            document.getElementById('Input_Othername'+Num_Stack).style.display = 'table';
            document.getElementById('textInput_Othername'+Num_Stack).style.display = 'table';
            document.getElementById('Othername_On'+Num_Stack).value = "";
        }else {
            document.getElementById('Input_Othername'+Num_Stack).style.display = 'none';
            document.getElementById('textInput_Othername'+Num_Stack).style.display = 'none';
            document.getElementById('Othername_On'+Num_Stack).value = "-";
        }
        $( "#ConFirm_Disabled" ).prop( "disabled", true );
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['donation/checkitemtype']) ?>',
            type: 'POST',
            data: 'Num_Stack='+Num_Stack+'&Item_ID='+data+'&Length_V2=<?=$Length_V2?>'+'&Title_Length=<?=$Title_Length?>'+'&FontS_rm=<?=$FontS_rm?>',
            success: function (data) {
                $('#Box_Itemtype_On'+Num_Stack).html(data);
            }
        });
    }

    function Check_Item(data,Num_Stack) {
        document.getElementById('Input_Othername'+Num_Stack).style.display = 'none';
        document.getElementById('textInput_Othername'+Num_Stack).style.display = 'none';
        document.getElementById('Othername_On'+Num_Stack).value = "-";
        $( "#ConFirm_Disabled" ).prop( "disabled", true );
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['donation/checkitem']) ?>',
            type: 'POST',
            data: 'Num_Stack='+Num_Stack+'&Itemtype_ID='+data+'&Length_V2=<?=$Length_V2?>'+'&Title_Length=<?=$Title_Length?>'+'&FontS_rm=<?=$FontS_rm?>',
            success: function (data) {
                $('#Box_Item_On'+Num_Stack).html(data);
            }
        });
    }

    function Check_cu_api(data,Item){
        if(data === "" || Item === ""){
            $( "#ConFirm_Disabled" ).prop( "disabled", true );
            document.getElementById('Submit_Succes').style.display = 'table';
        }else {
            $( "#ConFirm_Disabled" ).prop( "disabled", false );
            document.getElementById('Submit_Succes').style.display = 'table';
        }
    }

</script>

<style>

    /*.select2-container .select2-selection--single {*/
    /*border-style: solid !important;*/
    /*border-color: coral !important;*/
    /*}*/
    #Add_on {
        display: inline-block;
        width: 95%;
        text-align: right;
    }

    /* CSS Table */
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
        border-radius: 25px;
        border: 2px solid #000000;
    }

    td, th {
        border: 1px solid #797070;
        text-align: center;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }

    * {box-sizing: border-box;}
    /* ------------------------------------------------------------ */

    /* CSS Search */
    .topnav a {
        float: left;
        display: block;
        color: black;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        font-size: 17px;
    }

    .topnav a:hover {
        background-color: #ddd;
        color: black;
    }

    .topnav a.active {
        background-color: #2196F3;
        color: white;
    }

    .topnav .search-container {
        float: right;
    }

    .topnav input[type=text] {
        padding: 6px;
        margin-top: 8px;
        font-size: 17px;
        border: none;
    }

    .topnav .search-container button {
        float: right;
        padding: 6px 10px;
        margin-top: 8px;
        margin-right: 16px;
        background: #ddd;
        font-size: 17px;
        border: none;
        cursor: pointer;
    }

    .topnav .search-container button:hover {
        background: #ccc;
    }
    /* ------------------------------------------------------------ */

</style>