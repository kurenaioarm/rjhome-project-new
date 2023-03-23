<?php

/* @var $this yii\web\View */
use common\widgets\Alert;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use kartik\select2\Select2;

$this->title = 'My Yii Application';
$this->registerCssFile(
    '//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    '//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);

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
    <br><br>
    <div class="row">
        <div class="form-group mb-12">
            <?php $form = ActiveForm::begin(); ?>
            <?= Alert::widget() ?>
            <!----------------------------------------------------------------------------------------DateRangePicker----------------------------------------------------------------------------------------------------------------------->
            <div class="container">
                <?php
                // DateRangePicker in a dropdown format (uneditable/hidden input) and uses the preset dropdown.
                // Note that placeholder setting in this case will be displayed when value is null
                // Also the includeMonthsFilter setting will display LAST 3, 6 and 12 MONTHS filters.
                echo '<div class="drp-container">';
                echo DateRangePicker::widget([
                    'model'=>$model,
                    'attribute'=>'SelectedDate',
                    'presetDropdown'=>true,
                    'convertFormat'=>true,
                    //'includeMonthsFilter'=>true,
                    'pluginOptions' => ['locale' => ['format' => 'd/m/yy']],
                    'options' => ['placeholder' => 'Select range...']
                ]);
                echo '</div>';
                ?>
            </div><br>
            <!-----------------------------------------------------------------------------------------------Button----------------------------------------------------------------------------------------------------------------------------->
            <div class="container">
                <?= Html::submitButton('Check', ['class' => 'btn btn-dark btn-block', 'name' => 'Check-button', 'value'=>1, 'onClick'=>"CheckDate('target', 'replace_target')"]) ?>
            </div>
            <!-----------------------------------------------------------------------------------------------Loading---------------------------------------------------------------------------------------------------------------------------->
            <div>
                <span id="target"></span>
            </div>
            <div class="row" style="display:none">
                <span id="replace_target"><img src="https://rjhome.rajavithi.go.th/assets/images/Loading/Loading.gif" style="width: 250px; height: 70px"></span>
            </div>
            <!--------------------------------------------------------------------------------------------------Alert------------------------------------------------------------------------------------------------------------------------------>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <table class="table table-bordered" id="donationdata">
        <thead>
        <tr style="background: -webkit-linear-gradient(45deg, #5bccc8,#87dfdc); font-size:15px">
            <th style="color:black;text-align:center;" width="80 px">เลขหนังสือ</th>
            <th style="color:black;text-align:center;" width="80 px">ประเภทบุคคล</th>
            <th style="color:black;text-align:center;" width="250 px">ชื่อ</th>
            <th style="color:black;text-align:center;" width="80 px">วันที่ขอบริจาค</th>
            <th style="color:black;text-align:center;" width="200 px">ประเภทการรับหนังสือ</th>
            <th style="color:black;text-align:center;" width="200 px">การลงทะเบียนครุภัณฑ์</th>
            <th style="color:black;text-align:center;" width="250 px">รายละเอียด / แก้ไข</th>
        </tr>
        </thead>

        <tbody >
        <?php
        if (isset($API)){
        if($API->json_result == true ){
        foreach ($API->json_data as $data){ //วน tr
        ?>
        <tr onMouseover="this.style.backgroundColor='#343434';  this.style.color = 'white'; " onMouseout="this.style.backgroundColor='';  this.style.color = '';" >
            <td style="font-size:14px;padding: 18px;"><b><?php echo $data->DONATE_ID;?></b></td>

            <?php if( $data->NAME_TYPE == "1"){ ?>
                <td style="font-size:14px;padding: 18px;"><b>บุคคลธรรมดา</b></td>
            <?php }else{ ?>
                <td style="font-size:14px;padding: 18px;"><b>บริษัท,นิติบุคคล,มูลนิธิ</b></td>
            <?php } ?>

            <?php if( $data->LNAME == "-"){ ?>
                <td style="font-size:14px;padding: 18px;"><?php echo $data->NAME; ?> </td>
            <?php }else{ ?>
                <td style="font-size:14px;padding: 18px;"><?php echo $data->NAME." ".$data->LNAME;?> </td>
            <?php } ?>

            <td style="font-size:14px"><?php echo $data->DONATE_DATE;?> </td>

            <?php if( $data->LETTER_TYPE == "1"){ ?>
                <td style="font-size:14px;padding: 18px;
                        border-left: 4px solid rgba(49,49,49,0.49);
                        border-right: 2px solid rgba(49,49,49,0.49);
                        border-bottom: 2px solid rgba(49,49,49,0.49);
                        border-top: 2px solid rgba(49,49,49,0.49);
                        color:black; background: #abe060;"><b>ต้องการหนังสือตอบขอบคุณ</b></td>
            <?php }else{ ?>
                <td style="font-size:14px;padding: 18px;
                        border-left: 4px solid rgba(49,49,49,0.49);
                        border-right: 2px solid rgba(49,49,49,0.49);
                        border-bottom: 2px solid rgba(49,49,49,0.49);
                         border-top: 2px solid rgba(49,49,49,0.49);
                        color:black; background: #f33860;"><b>ไม่ต้องการหนังสือตอบขอบคุณ</b></td>
            <?php } ?>

            <?php if( $data->REGISTER_TYPE == "1"){ ?>
                <td style="font-size:14px;padding: 18px;
                        border-left: 2px solid rgba(49,49,49,0.49);
                        border-right: 4px solid rgba(49,49,49,0.49);
                        border-bottom: 2px solid rgba(49,49,49,0.49);
                        border-top: 2px solid rgba(49,49,49,0.49);
                        color:black; background: #abe060;"><b>ลงทะเบียนครุภัณฑ์</b></td>
            <?php }elseif ( $data->REGISTER_TYPE == "2"){ ?>
                <td style="font-size:14px;padding: 18px;
                        border-left: 2px solid rgba(49,49,49,0.49);
                        border-right: 4px solid rgba(49,49,49,0.49);
                        border-bottom: 2px solid rgba(49,49,49,0.49);
                        border-top: 2px solid rgba(49,49,49,0.49);
                        color:black; background: #f33860;"><b>ไม่ลงทะเบียนครุภัณฑ์</b></td>
            <?php }else{ ?>
                <td style="font-size:14px;padding: 18px;
                        border-left: 2px solid rgba(49,49,49,0.49);
                        border-right: 4px solid rgba(49,49,49,0.49);
                        border-bottom: 2px solid rgba(49,49,49,0.49);
                       border-top: 2px solid rgba(49,49,49,0.49);
                        color:black; background: #eefaff;"><b></b></td>
            <?php } ?>


            <td style="font-size:14px">
                <?php
                if (isset($Access_Token)) {
                    $DONATE_CONFIRM_ID = API_DONATE_CONFIRM_ID($data->DONATE_ID,"",$Access_Token['access_token']);
                }
                ?>

                <?php if (isset($DONATE_CONFIRM_ID)) {
                    if($DONATE_CONFIRM_ID->json_data == []){ ?>
                        <?php echo '&nbsp;&nbsp;&nbsp;' ?>
                        <span class="input-group-text btn btn-outline-danger   btn-sm" id="confirm-icon<?php echo $data->DONATE_ID ?>"><b>&nbsp;<i class="far fa-calendar-check" style="font-size: 1.3em;" aria-hidden="true" onclick="confirm_icon(<?php echo $data->DONATE_ID ?>)"></i>&nbsp;</b></span>
                        <!--------------------------------------------------------------------------------------------------------------------- CONFIRM ------------------------------------------------------------------------------------------------------------------------------>
                        <div id="confirm-popup<?php echo $data->DONATE_ID ?>">
                            <form class="contact-form" action="" id="contact-form"
                                  method="post" enctype="multipart/form-data">
                                <div class="mb-12">
                                    <h1>ยืนยันการได้รับสิ่งของบริจาค</h1>
                                </div>
                                <br>
                                <?php $form = ActiveForm::begin(); ?>
                                <?php echo  Html::hiddenInput('Donate_Id', $data->DONATE_ID); ?>
                                <div class="input-group">
                                    <label>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="width: 145px">วันที่รับของบริจาค</span>
                                        </div>
                                    </label>
                                    <label>
                                        <?php
                                        echo DateTimePicker::widget([
                                            'name' => 'DT_Confirm'.$data->DONATE_ID,
                                            'id' => 'DT_Confirm'.$data->DONATE_ID,
                                            'options' => [
                                                'placeholder' => 'Enter event time ...',
                                                'autocomplete' => 'off',
                                                'required' => true,
                                            ],
                                            'language' => 'th',
                                            'pluginOptions' => [
                                                'todayHighlight' => true,
                                                'todayBtn' => true,
                                                'format' => 'dd/mm/yyyy HH:ii',
                                                'autoclose' => true,
                                            ],
                                        ]);
                                        ?>
                                    </label>
                                    <label id="Input_Telephone_Staff" style="display:table;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info"  style="width: 145px">เบอร์โทรเจ้าหน้าที่</span>
                                        </div>
                                    </label>
                                    <label id="textInput_Telephone_Staff" style="display:table;">
                                        <?= $form->field($model, 'Telephone_Staff')->textInput(['class'=>'form-control' , 'id' => 'Telephone_Staff', 'value' => ' ' , 'maxlength' => '10'  , 'style'=>  'width: 250px' ,  'autocomplete'=>'off' , 'placeholder'=>'ระบุ : เบอร์โทรศัพท์' , 'onkeypress' =>"return CheckDate_Telephone(event)"])->label(false) ?>
                                    </label>
                                </div>
                                <br>

                                <div class="form-group mb-6" >
                                    <div class="row"  id="Loading2<?php echo $data->DONATE_ID ?>" style="display:none;text-align:center;width:100%;">
                                        <span ><img src="https://rjhome.rajavithi.go.th/assets/images/Loading/Loading.gif" style="width: 250px; height: 70px"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col" id="Submit_Confirm<?php echo $data->DONATE_ID ?>" style="display:table;">
                                            <?= Html::submitButton('<b>ยืนยันการรับบริจาค <i class="fas fa-check-square" style="font-size:19px;"></i></b>', ['class' => 'input-group-text btn btn-outline-success btn-sm',  'name' => 'Check-button' ,'value'=>4 ,'disabled' => false , 'style'=>'width: 100%;','onClick'=>"CheckSubmit2($data->DONATE_ID)"]) ?>
                                        </div>
                                        <div class="col" id="Submit_Cancel<?php echo $data->DONATE_ID ?>" style="display:table;">
                                            <span class="input-group-text btn btn-outline-danger btn-sm" id="cancel_icon2<?php echo $data->DONATE_ID ?>" onclick="cancel_icon2(<?php echo $data->DONATE_ID ?>)" style="width: 100%"><b>Cancel</b></span>
                                        </div>
                                        <br>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </form>
                        </div>
                    <?php }else{ ?>
                        <?php echo '&nbsp;&nbsp;&nbsp;' ?>
                        <span class="input-group-text btn btn-outline-success  btn-sm" id="confirm-icon<?php echo $data->DONATE_ID ?>"><b>&nbsp;<i class="fas fa-edit" style="font-size: 1.3em;" aria-hidden="true" onclick="confirm_icon(<?php echo $data->DONATE_ID ?>)"></i>&nbsp;</b></span>
                        <!--------------------------------------------------------------------------------------------------------------------- CONFIRM UPDATE  ------------------------------------------------------------------------------------------------------------------------------>
                        <div id="confirm-popup<?php echo $data->DONATE_ID ?>">
                            <form class="contact-form" action="" id="contact-form"
                                  method="post" enctype="multipart/form-data">
                                <div class="mb-12">
                                    <h1>ยืนยันการได้รับสิ่งของบริจาค</h1>
                                </div>
                                <br>
                                <?php $form = ActiveForm::begin(); ?>
                                <?php echo  Html::hiddenInput('Donate_Id', $data->DONATE_ID); ?>
                                <div class="input-group">
                                    <label>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="width: 145px">วันที่รับของบริจาค</span>
                                        </div>
                                    </label>
                                    <label>
                                        <?php
                                        if (isset($DONATE_CONFIRM_ID)) {
                                            echo DateTimePicker::widget([
                                                'name' => 'DT_Confirm'.$data->DONATE_ID,
                                                'id' => 'DT_Confirm'.$data->DONATE_ID,
                                                'value' => $DONATE_CONFIRM_ID->json_data[0]->CON_DATED, // initial value
                                                'disabled' => true,
                                                'options' => [
                                                    'placeholder' => 'Enter event time ...',
                                                    'autocomplete' => 'off',
                                                    'required' => true,
                                                ],
                                                'language' => 'th',
                                                'pluginOptions' => [
                                                    'todayHighlight' => true,
                                                    'todayBtn' => true,
                                                    'format' => 'dd/mm/yyyy HH:ii',
                                                    'autoclose' => true,
                                                ],
                                            ]);
                                        }
                                        ?>
                                    </label>
                                    <label id="Input_Telephone_Staff" style="display:table;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info"  style="width: 145px">เบอร์โทรเจ้าหน้าที่</span>
                                        </div>
                                    </label>
                                    <label id="textInput_Telephone_Staff" style="display:table;">
                                        <?= $form->field($model, 'Telephone_Staff')->textInput(['class'=>'form-control' , 'id' => 'Telephone_Staff', 'value' =>$DONATE_CONFIRM_ID->json_data[0]->CON_TELEPHONE , 'maxlength' => '10'  , 'style'=>  'width: 250px' ,  'autocomplete'=>'off' , 'placeholder'=>'ระบุ : เบอร์โทรศัพท์' , 'onkeypress' =>"return CheckDate_Telephone(event)"])->label(false) ?>
                                    </label>
                                </div>
                                <br>
                                <div class="form-group mb-6" >
                                    <div class="row"  id="Loading2<?php echo $data->DONATE_ID ?>" style="display:none;text-align:center;width:100%;">
                                        <span ><img src="https://rjhome.rajavithi.go.th/assets/images/Loading/Loading.gif" style="width: 250px; height: 70px"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col" id="Submit_Confirm2<?php echo $data->DONATE_ID ?>" style="display:table;">
                                            <?= Html::submitButton('<b>แก้ไขเบอร์โทร <i class="fas fa-file-signature" style="font-size:19px;"></i></b>', ['class' => 'input-group-text btn btn-outline-success btn-sm',  'name' => 'Check-button' ,'value'=>5 ,'disabled' => false , 'style'=>'width: 100%;' ,'onClick'=>"CheckSubmit2($data->DONATE_ID)"]) ?>
                                        </div>
                                        <div class="col" id="Submit_Cancel<?php echo $data->DONATE_ID ?>" style="display:table;">
                                            <span class="input-group-text btn btn-outline-danger btn-sm" id="cancel_icon2<?php echo $data->DONATE_ID ?>" onclick="cancel_icon2(<?php echo $data->DONATE_ID ?>)" style="width: 100%"><b>Cancel</b></span>
                                        </div>
                                        <br>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </form>
                        </div>
                    <?php }
                } ?>

                <?php echo '&nbsp;&nbsp;&nbsp;' ?>
                <span class="input-group-text btn btn-outline-warning btn-sm" id="update-icon<?php echo $data->DONATE_ID ?>"><b>&nbsp;<i class="fas fa-file-signature" style="font-size: 1.3em;" aria-hidden="true" onclick="update_icon(<?php echo $data->DONATE_ID ?>)"></i>&nbsp;</b></span>
                <style>
                    /* --------------------------------------------------------------- Popup Contact ---------------------------------------------------------------------- */
                    body {
                        color: #232323;
                        font-size: 0.95em;
                        font-family: arial;
                    }
                    div#success {
                        text-align: center;
                        box-shadow: 1px 1px 5px #455644;
                        background: #bae8ba;
                        padding: 10px;
                        border-radius: 3px;
                        margin: 0 auto;
                        width: 350px;
                    }
                    .inputBox {
                        width: 100%;
                        margin: 5px 0px 15px 0px;
                        border: #dedede 1px solid;
                        box-sizing: border-box;
                        padding: 15px;
                    }
                    #contact-popup<?php echo $data->DONATE_ID ?> {
                        position: absolute;
                        top: 0px;
                        left: 0px;
                        height: 100%;
                        width: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: none;
                        color: #676767;
                    }
                    #confirm-popup<?php echo $data->DONATE_ID ?> {
                        position: absolute;
                        top: 0px;
                        left: 0px;
                        height: 100%;
                        width: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: none;
                        color: #676767;
                    }
                    .contact-form {
                        width: 70%;
                        margin: 0px;
                        background-color: white;
                        font-family: Arial;
                        position: relative;
                        left: 15%;
                        top: -20%;
                        /*margin-left: -760px;*/
                        /*margin-top: -400px;*/
                        box-shadow: 1px 1px 5px #444444;
                        padding: 20px 40px 40px 40px;
                    }
                    .info {
                        color: #d30a0a;
                        letter-spacing: 2px;
                        padding-left: 5px;
                    }
                    #send {
                        background-color: #09F;
                        border: 1px solid #1398f1;
                        font-family: Arial;
                        color: white;
                        width: 100%;
                        padding: 10px;
                        cursor: pointer;
                    }
                    #contact-popup<?php echo $data->DONATE_ID ?> h1 {
                        font-weight: normal;
                        text-align: center;
                        margin: 10px 0px 20px 0px;
                    }
                    #confirm-popup<?php echo $data->DONATE_ID ?> h1 {
                        font-weight: normal;
                        text-align: center;
                        margin: 10px 0px 20px 0px;
                    }
                    .input-error {
                        border: #e66262 1px solid;
                    }
                </style>

                <!--------------------------------------------------------------------------------------------------------------------- UPDATE ------------------------------------------------------------------------------------------------------------------------------>
                <div id="contact-popup<?php echo $data->DONATE_ID ?>">
                    <form class="contact-form" action="" id="contact-form"
                          method="post" enctype="multipart/form-data">
                        <div class="mb-12">
                            <h1>แก้ไขข้อมูล</h1>
                        </div>

                        <?php $form = ActiveForm::begin(); ?>
                        <?php echo  Html::hiddenInput('DONATE_ID', $data->DONATE_ID); ?>
                        <div class="row" >
                            <div class="input-group">
                                <label>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="width: 170px">วัน-เวลา ที่ทำรายการ</span>
                                    </div>
                                </label>
                                <label>
                                    <?= $form->field($model, 'Report_Date')->textInput(['class'=>'form-control border-info' , 'id' => 'Report_Date', 'style'=> 'width: 250px' , 'value'=>$data->DONATE_DATE , 'autocomplete'=>'off' , 'disabled' => true])->label(false) ?>
                                </label>

                                <label>
                                    <div class=" input-group-prepend">
                                        <span class="input-group-text btn btn-info"  style="width: 170px">ประเภทบุคคล</span>
                                    </div>
                                </label>
                                <label>
                                    <?php
                                    $group_prepend_data = [
                                        "1" => "บุคคลธรรมดา",
                                        "2" => "นิติบุคคล/บริษัท/มูลนิธ"
                                    ];
                                    echo Select2::widget([
                                        'id' => 'Name_Type'.$data->DONATE_ID,
                                        'name' => 'Name_Type'.$data->DONATE_ID,
                                        'value' => $data->NAME_TYPE, // initial value
                                        'data' => $group_prepend_data,
                                        'size' => Select2::MEDIUM,
                                        'options' => [
                                            'placeholder' => 'Select a state ...',
                                            'onchange' =>"Check_Name_Type(this.value,$data->DONATE_ID,'$data->LNAME',$data->TAXPAYER_NUMBER)",
                                            'required' => true ,
                                        ],
                                        'pluginOptions' => [
                                            'width' => '250px',
                                        ],
                                    ]);
                                    ?>
                                </label>
                            </div>

                            <div class="input-group">
                                <label id="Input_Telephone" style="display:table;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info"  style="width: 170px">เบอร์โทรศัพท์</span>
                                    </div>
                                </label>
                                <label id="textInput_Telephone" style="display:table;">
                                    <?= $form->field($model, 'Telephone')->textInput(['class'=>'form-control' , 'id' => 'Telephone'.$data->DONATE_ID, 'value' =>$data->TELEPHONE , 'maxlength' => '10'  , 'style'=>  'width: 250px' ,  'autocomplete'=>'off' , 'required' => true , 'placeholder'=>'ระบุ : เบอร์โทรศัพท์' , 'onkeypress' =>"return CheckDate_Telephone(event)"])->label(false) ?>
                                </label>
                                <label id="Input_My_Pname">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info"  style="width: 170px">คำนำหน้าชื่อ</span>
                                    </div>
                                </label>
                                <label >
                                    <?php
                                    if (isset($Array_pname_api)) {
                                        echo Select2::widget([
                                            'id' => 'My_Pname'.$data->DONATE_ID,
                                            'name' => 'My_Pname'.$data->DONATE_ID,
                                            'value' => $data->PNAME, // initial value
                                            'data' => $Array_pname_api,
                                            'size' => Select2::MEDIUM,
                                            'disabled' => false,
                                            'options' => [
                                                'placeholder' => 'Select a state ...',
                                                'required' => true ,
                                            ],
                                            'pluginOptions' => [
                                                'width' => '248px',
                                            ],
                                        ]);
                                    }
                                    ?>
                                </label>
                            </div>
                            <div class="input-group mb-3">
                                <?php if($data->NAME_TYPE == "1"){?>
                                    <label id="Input_My_Name" style="display:table;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="width: 170px">ชื่อ</span>
                                        </div>
                                    </label>
                                    <label class="align-items-center" id="textInput_My_Name" style="display:table;">
                                        <?= $form->field($model, 'My_Name')->textInput(['class'=>'form-control border-info' , 'id' => 'My_Name'.$data->DONATE_ID, 'value' =>$data->NAME , 'style'=> 'width: 250px' , 'autocomplete'=>'off', 'required' => true  , 'disabled' => false])->label(false) ?>
                                    </label>
                                    <label  id="Input_My_Lname" style="display:table;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="width: 170px">นามสกุล</span>
                                        </div>
                                    </label>
                                    <label class="align-items-center"  id="textInput_My_Lname" style="display:table;">
                                        <?= $form->field($model, 'My_Lname')->textInput(['class'=>'form-control border-info' , 'id' => 'My_Lname'.$data->DONATE_ID, 'value' =>$data->LNAME , 'style'=>  'width: 250px' , 'autocomplete'=>'off', 'required' => true  , 'disabled' => false])->label(false) ?>
                                    </label>
                                    <label id="Input_Taxpayer_Number" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="width: 170px">เลขผู้เสียภาษี</span>
                                        </div>
                                    </label>
                                    <label id="textInput_Taxpayer_Number" style="display:none;">
                                        <?= $form->field($model, 'Taxpayer_Number')->textInput(['class'=>'form-control border-info' , 'id' => 'Taxpayer_Number'.$data->DONATE_ID, 'value' =>'0' , 'maxlength' => '13'   , 'style'=>  'width: 250px' ,  'autocomplete'=>'off', 'required' => true  , 'onkeypress' =>"return CheckDate_Taxpayer(event)"])->label(false) ?>
                                    </label>
                                <?php }else{ ?>
                                    <label id="Input_My_Name" style="display:table;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="width: 170px">ชื่อ</span>
                                        </div>
                                    </label>
                                    <label class="align-items-center" id="textInput_My_Name" style="display:table;">
                                        <?= $form->field($model, 'My_Name')->textInput(['class'=>'form-control border-info' , 'id' => 'My_Name'.$data->DONATE_ID,  'value' =>$data->NAME , 'style'=> 'width: 250px' , 'autocomplete'=>'off', 'required' => true  , 'disabled' => false])->label(false) ?>
                                    </label>
                                    <label  id="Input_My_Lname" style="display:none;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="width: 170px">นามสกุล</span>
                                        </div>
                                    </label>
                                    <label class="align-items-center"  id="textInput_My_Lname" style="display:none;">
                                        <?= $form->field($model, 'My_Lname')->textInput(['class'=>'form-control border-info' , 'id' => 'My_Lname'.$data->DONATE_ID, 'value' =>'-' , 'style'=>  'width: 250px' , 'autocomplete'=>'off', 'required' => true  , 'disabled' => false])->label(false) ?>
                                    </label>
                                    <label id="Input_Taxpayer_Number" style="display:table;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" style="width: 170px">เลขผู้เสียภาษี</span>
                                        </div>
                                    </label>
                                    <label id="textInput_Taxpayer_Number" style="display:table;">
                                        <?= $form->field($model, 'Taxpayer_Number')->textInput(['class'=>'form-control border-info' , 'id' => 'Taxpayer_Number'.$data->DONATE_ID, 'value' =>$data->TAXPAYER_NUMBER, 'maxlength' => '13'   , 'style'=>  'width: 250px' ,  'autocomplete'=>'off', 'required' => true  , 'onkeypress' =>"return CheckDate_Taxpayer(event)"])->label(false) ?>
                                    </label>
                                <?php } ?>
                            </div>

                            <hr>
                            <div class="input-group mb-3">
                            </div>
                            <div class="input-group mb-3 ">
                                <label id="Input_Letter_Type" style="display:table;">
                                    <div class=" input-group-prepend">
                                        <span class="input-group-text btn btn-info"  style="width: 170px">ประเภทการรับหนังสือ</span>
                                    </div>
                                </label>
                                <label>
                                    <?php
                                    $group_prepend_data = [
                                        "1" => "ต้องการหนังสือตอบขอบคุณ",
                                        "2" => "ไม่ต้องการหนังสือตอบขอบคุณ"
                                    ];
                                    echo Select2::widget([
                                        'id' => 'Letter_Type'.$data->DONATE_ID,
                                        'name' => 'Letter_Type'.$data->DONATE_ID,
                                        'value' => $data->LETTER_TYPE , // initial value
                                        'data' => $group_prepend_data,
                                        'size' => Select2::MEDIUM,
                                        'options' => [
                                            'placeholder' => 'Select a state ...',
//                                            'onchange' =>"CheckRadio_Two(this.value)",
                                            'required' => true ,
                                        ],
                                        'pluginOptions' => [
                                            'width' => '250px',
                                        ],
                                    ]);
                                    ?>
                                </label>

                                <label id="Input_Register_Type" style="display:table;">
                                    <div class=" input-group-prepend">
                                        <span class="input-group-text btn btn-info"  style="width: 170px">การลงทะเบียน</span>
                                    </div>
                                </label>
                                <label>
                                    <?php
                                    $group_prepend_data = [
                                        "1" => "ลงทะเบียนครุภัณฑ์",
                                        "2" => "ไม่ลงทะเบียนครุภัณฑ์"
                                    ];
                                    echo Select2::widget([
                                        'id' => 'Register_Type'.$data->DONATE_ID,
                                        'name' => 'Register_Type'.$data->DONATE_ID,
                                        'value' => $data->REGISTER_TYPE , // initial value
                                        'data' => $group_prepend_data,
                                        'size' => Select2::MEDIUM,
                                        'options' => [
                                            'placeholder' => 'Select a state ...',
                                        ],
                                        'pluginOptions' => [
                                            'width' => '250px',
                                            'allowClear' => true
                                        ],
                                    ]);
                                    ?>
                                </label>
                            </div>


                            <div class="input-group">
                                <label id="Input_Address_No"  style="display:table;">
                                    <div class="input-group-prepend" >
                                        <span class="input-group-text btn btn-info" style="width: 170px">ที่อยู่ เลขที่</span>
                                    </div>
                                </label>
                                <label class="align-items-center" id="textInput_Address_No"  style="display:table;">
                                    <?php if($data->ADDRESS_NO == null){  ?>
                                        <?= $form->field($model, 'Address_No')->textInput(['class'=>'form-control border-info' , 'id' => 'Address_No'.$data->DONATE_ID, 'value' => ' ' , 'style'=> 'width: 250px' , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                    <?php }else{ ?>
                                        <?= $form->field($model, 'Address_No')->textInput(['class'=>'form-control border-info' , 'id' => 'Address_No'.$data->DONATE_ID, 'value' => $data->ADDRESS_NO , 'style'=> 'width: 250px' , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                    <?php } ?>
                                </label>
                                <label id="Input_Alley"  style="display:table;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="width: 170px">ซอย</span>
                                    </div>
                                </label>
                                <label class="align-items-center" id="textInput_Alley"  style="display:table;">
                                    <?php if($data->ALLEY == null){  ?>
                                        <?= $form->field($model, 'Alley')->textInput(['class'=>'form-control border-info' , 'id' => 'Alley'.$data->DONATE_ID, 'value' => ' ' , 'style'=> 'width: 250px' , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                    <?php }else{ ?>
                                        <?= $form->field($model, 'Alley')->textInput(['class'=>'form-control border-info' , 'id' => 'Alley'.$data->DONATE_ID, 'value' => $data->ALLEY , 'style'=> 'width: 250px' , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                    <?php } ?>
                                </label>
                            </div>


                            <div class="input-group">
                                <label  id="Input_Road"  style="display:table;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="width: 170px">ถนน</span>
                                    </div>
                                </label>
                                <label class="align-items-center"  id="textInput_Road"  style="display:table;">
                                    <?php if($data->ROAD == null){  ?>
                                        <?= $form->field($model, 'Road')->textInput(['class'=>'form-control border-info' , 'id' => 'Road'.$data->DONATE_ID, 'value' => ' ' , 'style'=> 'width: 250px' , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                    <?php }else{ ?>
                                        <?= $form->field($model, 'Road')->textInput(['class'=>'form-control border-info' , 'id' => 'Road'.$data->DONATE_ID, 'value' => $data->ROAD , 'style'=> 'width: 250px' , 'autocomplete'=>'off' , 'disabled' => false])->label(false) ?>
                                    <?php } ?>
                                </label>
                                <?php
                                if($data->LETTER_TYPE == 1){
                                    $PROVINCE = $data->PROVINCE;
                                    $DISTRICT = $data->DISTRICT;
                                    $TAMBON = $data->TAMBON;
                                    $DNAME = $data->DNAME;
                                    $TNAME = $data->TNAME;
                                }else{
                                    $PROVINCE = "";
                                    $DISTRICT = "";
                                    $TAMBON = "";
                                    $DNAME = "";
                                    $TNAME = "";
                                }
                                //============================================== สร้าง Array ตำบลทั้งหมด =======================================================
                                $Array_tumbon_api = array();
                                $Array_tumbon_api[$TAMBON]=$TNAME; //การสร้าง Array แบบกำหนด  key value
                                //============================================== สร้าง Array อำเภอทั้งหมด =======================================================
                                $Array_ampur_api = array();
                                $Array_ampur_api[$DISTRICT]=$DNAME; //การสร้าง Array แบบกำหนด  key value
                                ?>
                                <label id="Input_Changwat_Type" style="display:table;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="width: 170px">จังหวัด</span>
                                    </div>
                                </label>
                                <label class="align-items-center" id="textInput_Changwat_Type" style="display:table;">
                                    <?php
                                    if (isset($Array_changwat_api)) {
                                        echo '<div id="Box_Incident_Changwat">';
                                        echo $this->render('_Incident_Changwat', [
                                            'Array_changwat_api' => $Array_changwat_api,
                                            'DONATE_ID' => $data->DONATE_ID,
                                            'PROVINCE'  => $PROVINCE,
                                        ]) ;
                                        echo '</div>';
                                    }
                                    ?>
                                </label>
                            </div>


                            <div class="input-group mb-3">
                                <label id="Input_Ampur_Type" style="display:table;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="width: 170px">อำเภอ/เขต</span>
                                    </div>
                                </label>
                                <label class="align-items-center" id="textInput_Ampur_Type" style="display:table;">
                                    <div id="Box_Incident_Ampur<?php echo $data->DONATE_ID ?>">
                                        <?php
                                        if (isset($Array_ampur_api)) {
                                            echo $this->render('_Incident_Ampur', [
                                                'Array_ampur_api' => $Array_ampur_api,
                                                'Array_changwat_api' => "",
                                                'Check_Status'=>"",
                                                'DONATE_ID' => $data->DONATE_ID,
                                                'DISTRICT'  => $DISTRICT,
                                            ]) ;
                                        }
                                        ?>
                                    </div>
                                </label>
                                <label  id="Input_Tambon_Type" style="display:table;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="width: 170px">ตำบล/แขวง</span>
                                    </div>
                                </label>
                                <label class="align-items-center" id="textInput_Tambon_Type" style="display:table;">
                                    <div id="Box_Incident_Tumbon<?php echo $data->DONATE_ID ?>">
                                        <?php
                                        if (isset($Array_tumbon_api)) {
                                            echo $this->render('_Incident_Tumbon', [
                                                'Array_tumbon_api' => $Array_tumbon_api,
                                                'Array_ampur_api' => "",
                                                'Array_changwat_api' => "",
                                                'Check_Status'=>"",
                                                'DONATE_ID' => $data->DONATE_ID,
                                                'TAMBON'  => $TAMBON,
                                            ]) ;
                                        }
                                        ?>
                                    </div>
                                </label>
                            </div>


                            <div class="input-group mb-3">
                                <?php
                                //============================================== สร้าง Array ZIPCODE =======================================================
                                $Array_zipcode_api = array();
                                $DataID_Zipcode =  $data->ZIP_CODE;
                                $DataNAME_Zipcode =  $data->ZIP_CODE;
                                $Array_zipcode_api[$DataID_Zipcode]=$DataNAME_Zipcode; //การสร้าง Array แบบกำหนด  key value
                                ?>
                                <label id="Input_Zip_Code" style="display:table;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn btn-info" style="width: 170px">รหัสไปรษณีย์</span>
                                    </div>
                                </label>
                                <label id="textInput_Zip_Code" style="display:table;">
                                    <div id="Box_Incident_Zipcode<?php echo $data->DONATE_ID ?>">
                                        <?php
                                        echo $this->render('_Incident_Zipcode', [
                                            'Array_zipcode_api' => $Array_zipcode_api,
                                            'Check_Status'=>"",
                                            'DONATE_ID' => $data->DONATE_ID,
                                        ]) ;
                                        ?>
                                    </div>
                                </label>
                            </div>

                            <div class="input-group mb-3 ">
                                <label id="Input_Letter_Donate_Note" style="display:table;">
                                    <div class="">
                                        <span class="btn btn-info"  style="width: 850px">หมายเหตุ : บริจาคเนื่องในโอกาส</span>
                                    </div>
                                    <?php if($data->DONATE_NOTE == null){  ?>
                                        <?= $form->field($model, 'Donate_Note')->textArea(['class'=>'' , 'id' => 'Donate_Note'.$data->DONATE_ID, 'value' => ' ' , 'style'=> 'width: 850px' ,  'autocomplete'=>'off' , 'onchange' =>"CheckDate_ALL()"])->label(false) ?>
                                    <?php }else{ ?>
                                        <?= $form->field($model, 'Donate_Note')->textArea(['class'=>'' , 'id' => 'Donate_Note'.$data->DONATE_ID, 'value' => $data->DONATE_NOTE , 'style'=> 'width: 850px' ,  'autocomplete'=>'off' , 'onchange' =>"CheckDate_ALL()"])->label(false) ?>
                                    <?php } ?>
                                </label>
                            </div>
                        </div>


                        <div class="row"  id="Loading<?php echo $data->DONATE_ID ?>" style="display:none;text-align:center;width:100%;">
                            <span ><img src="https://rjhome.rajavithi.go.th/assets/images/Loading/Loading.gif" style="width: 250px; height: 70px"></span>
                        </div>
                        <div class="form-group mb-6" >
                            <div class="row">
                                <div class="col" id="Submit_Edit<?php echo $data->DONATE_ID ?>" style="display:table;">
                                    <?= Html::submitButton('<b>แก้ไขข้อมูล <i class="fas fa-file-signature" style="font-size:19px;"></i></b>', ['class' => 'input-group-text btn btn-outline-warning btn-sm', 'name' => 'Check-button' ,'value'=>2,'disabled' => false , 'style'=>'width: 100%;','onClick'=>"CheckSubmit($data->DONATE_ID)"]) ?>
                                </div>
                                <div class="col" id="Submit_Cancel<?php echo $data->DONATE_ID ?>" style="display:table;">
                                    <span class="input-group-text btn btn-outline-danger btn-sm" id="cancel_icon<?php echo $data->DONATE_ID ?>" onclick="cancel_icon(<?php echo $data->DONATE_ID ?>)" style="width: 100%"><b>Cancel</b></span>
                                </div>
                                <br>
                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </form>
                </div>


                <!-------------------------------------------------------------------------------------------------------------------------------- PDF Report --------------------------------------------------------------------------------------------------------------------------------->
                <?php echo '&nbsp;&nbsp;&nbsp;' ?>
                <?= Html::a('&nbsp;<i class="fas fa-print"  style="font-size: 1.3em;" aria-hidden="true"></i>&nbsp;','fpdfreport',
                    [
                        'class' => 'input-group-text btn btn-outline-primary  btn-sm',
                        'target'=>'_blank',
                        'data'=>[
                            'method' => 'post',
                            'target'=>'_blank',
                            'params'=>[
                                'User[DONATE_ID]' => $data->DONATE_ID,
                                'User[NAME_TYPE]' => $data->NAME_TYPE,
                            ],
                        ],
                    ]) ?>
            </td>
            <?php }}} ?>
        </tr>
        </tbody>
    </table>
</div>






    <?php
    function API_DONATE_CONFIRM_ID($DONATE_ID,$CONFIRMTYPE_ID,$Token){
        $curl = curl_init();
        $ADMINToken = 'DONATE_ID='.$DONATE_ID.'&CONFIRMTYPE_ID='.$CONFIRMTYPE_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_confirm',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $ADMINToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    $this->registerJS('
                $(function() {
                    $("#donationdata").dataTable({
                        "order": [[0, "desc"]],
                        "bPaginate": true,
                        "bLengthChange": true,
//                        "bFilter": false,
                        "bSort": true,
                        "bInfo": true,
                        "bAutoWidth": true,
//                        "pagingType": "full_numbers"
                    });
                });
            ');
    ?>



<script type="text/javascript">

    $(function(){
        //========================================รอโหลดหน้า===============================================
        $("#overlay").fadeOut();
        $(".main-contain").removeClass("main-contain");
        //===============================================================================================
    });

    function CheckDate(target, source) {
        //========================================ซ่อนและแสดงรูป===============================================
        document.getElementById(target).innerHTML = document.getElementById(source).innerHTML;
        //===============================================================================================
    }

    function CheckSubmit(DONATE_ID) {
        document.getElementById('Submit_Confirm'+DONATE_ID).style.display = 'table';
        document.getElementById('Submit_Edit'+DONATE_ID).style.display = 'none';
        document.getElementById('Submit_Cancel'+DONATE_ID).style.display = 'none';
        document.getElementById('Loading'+DONATE_ID).style.display = 'table';
    }

    function CheckSubmit2(DONATE_ID) {
        document.getElementById('Submit_Confirm2'+DONATE_ID).style.display = 'none';
        document.getElementById('Loading2'+DONATE_ID).style.display = 'table';
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
    function CheckDate_Taxpayer(e) {
        //======================================== ไม่ให้พิมตัวอักษร ===========================================
        let vchar = String.fromCharCode(event.keyCode);
//        if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
        if ((vchar < '0' || vchar > '9')) return false;
        e.onKeyPress = vchar;
        //===============================================================================================
    }

    function confirm_icon(DONATE_ID){
//        document.getElementById('Submit_Confirm'+DONATE_ID).style.display = 'table';
        $("#confirm-popup"+DONATE_ID).show();
    }
    function update_icon(DONATE_ID){
        document.getElementById('Submit_Edit'+DONATE_ID).style.display = 'table';
        document.getElementById('Submit_Cancel'+DONATE_ID).style.display = 'table';
        $("#contact-popup"+DONATE_ID).show();
    }
    function cancel_icon(DONATE_ID) {
        $("#contact-popup"+DONATE_ID).hide();
    }
    function cancel_icon2(DONATE_ID) {
        $("#confirm-popup"+DONATE_ID).hide();
    }

    function  Check_Name_Type(data,DONATE_ID,LNAME,TAXPAYER_NUMBER) {
        if(data === "1"){//บุคคลธรรมดา
            document.getElementById('Input_My_Lname').style.display = 'table';
            document.getElementById('textInput_My_Lname').style.display = 'table';
            document.getElementById('My_Lname'+DONATE_ID).value = LNAME;

            document.getElementById('Input_Taxpayer_Number').style.display = 'none';
            document.getElementById('textInput_Taxpayer_Number').style.display = 'none';
            document.getElementById('Taxpayer_Number'+DONATE_ID).value = "0";
        }else {//นิติบุคคล/บริษัท/มูลนิธิ
            document.getElementById('Input_My_Lname').style.display = 'none';
            document.getElementById('textInput_My_Lname').style.display = 'none';
            document.getElementById('My_Lname'+DONATE_ID).value = "-";

            document.getElementById('Input_Taxpayer_Number').style.display = 'table';
            document.getElementById('textInput_Taxpayer_Number').style.display = 'table';
            document.getElementById('Taxpayer_Number'+DONATE_ID).value = TAXPAYER_NUMBER;
        }
    }

    function CheckRadio_Two(data) {
        if(data === "1") {//ต้องการหนังสือตอบขอบคุณ
            alert(1);
        }else {//ไม่ต้องการหนังสือตอบขอบคุณ
            alert(2);
        }
    }


    function Check_ampur(data,DONATE_ID) {//อำเภอทั้งหมด
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['donationdata/checkampur']) ?>',
            type: 'POST',
            data: 'Changwat_ID='+data+'&DONATE_ID='+DONATE_ID,
            success: function (data) {
                $('#Box_Incident_Ampur'+DONATE_ID).html(data);
            }
        });
    }
    function Check_tumbon(data,changwat,DONATE_ID) {//ตำบลทั้งหมด
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['donationdata/checktumbon']) ?>',
            type: 'POST',
            data: 'Ampurstep2_ID='+data+'&Changwatstep2_ID='+changwat+'&DONATE_ID='+DONATE_ID,
            success: function (data) {
                $('#Box_Incident_Tumbon'+DONATE_ID).html(data);
            }
        });
    }
    function Check_zipcode(data,changwat,ampur,DONATE_ID){
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['donationdata/checkzipcode']) ?>',
            type: 'POST',
            data: 'Tumbonstep3_ID='+data+'&Changwatstep3_ID='+changwat+'&Ampurstep3_ID='+ampur+'&DONATE_ID='+DONATE_ID,
            success: function (data) {
                $('#Box_Incident_Zipcode'+DONATE_ID).html(data);
            }
        });
    }

</script>

<style>

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

