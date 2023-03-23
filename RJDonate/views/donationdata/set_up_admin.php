<?php

/* @var $this yii\web\View */
use common\widgets\Alert;
use yii\widgets\ActiveForm;
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
    <br>
    <div class="row">

        <!---------------------------------------------------------------------------------- Popup Add Permission ----------------------------------------------------------------------------------------------------------->
        <b style="font-size:22px;">ระบบงาน สำหรับผู้ดูแลระบบ (Admin)</b>
        <p class="lead" style="font-size:16px;"><b><u>หน้านี้</u></b> : เป็นระบบงาน สำหรับผู้ดูแลระบบ <b>(Admin)</b> ซึ่งเกี่ยวข้องกับ การกำหนดสิทธิ์ให้กับ <b>user</b> หรือผู้ใช้ การเปิด-ปิดการใช้งาน.</p>
        <!---------Alert----------->
        <?= Alert::widget() ?>
        <!---------------------------------------------------- Button เพิ่มสิทธิ์ การเข้าใช้   ------------------------------------------------------------------>
        <label id="Add_on">
            <!--https://www.w3schools.com/icons/icons_reference.asp => ICON PHP -->
            <span class="input-group-text btn btn-outline-success btn-sm" id="contact-icon" style="width: 170px;"><b>เพิ่มสิทธิ์ การเข้าใช้ <i class="fas fa-user-plus" style="font-size:19px;"></i></b></span>
        </label>
        <!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->

        <div class="form-group mb-12">
            <?php $form = ActiveForm::begin(); ?>


            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <table>
        <tbody>
        <tr style="border: 2px solid #000000; background: -webkit-linear-gradient(45deg, #5bccc8,#87dfdc); font-size:15px">
            <th style="color:black" width="250 px">ID CARD</th>
            <th style="color:black" width="350 px">ชื่อ-นามสกุล บุคลากร</th>
            <th style="color:black" width="250 px">หน่วยงาน</th>
            <th style="color:black" width="250 px">ระดับสิทธิ์</th>
            <th style="color:black" width="250 px">การจัดการ</th>
        </tr>

        <?php
        if (isset($API_donate_admin)){
        if($API_donate_admin->json_result == true ){
        foreach ($API_donate_admin->json_data as $data){ //วน tr
        ?>
        <tr onMouseover="this.style.backgroundColor='#343434';  this.style.color = 'white'; " onMouseout="this.style.backgroundColor='';  this.style.color = '';">
            <td style="font-size:14px"><?php echo $data->ADMIN_ID;?> </td>
            <td style="font-size:14px"><?php echo $data->ADMIN_NAME;?> </td>
            <td style="font-size:14px">
                <?php
                $API_agency_name = API_agency_name($data->ADMIN_AGENCY_ID,Yii::$app->session->get('arrayAccess_Token')['access_token']);
                if($API_agency_name->json_data == null){
                    echo $data->ADMIN_AGENCY_ID;
                }else{
                    echo $API_agency_name->json_data[0]->DSPNAME;
                }
                ?>
            </td>

            <?php if( $data->TYPE_ID == "1"){ ?>
                <td style="font-size:14px;color: magenta;"><b>SuperAdmin</b></td>
            <?php }else{ ?>
                <td style="font-size:14px;color: green;"><b>Admin</b></td>
            <?php } ?>

            <?php $Access_Token =  Yii::$app->session->get('arrayAccess_Token');?>
            <?php if($Access_Token['admin']->json_data[0]->TYPE_ID === "1" && $Access_Token['admin']->json_data[0]->ADMIN_ID !== $data->ADMIN_ID && $data->ADMIN_ID != "1103700041583"){ ?>
                <td style="font-size:14px">
                    <?= Html::a('&nbsp;<i class="fas fa-user-slash"  style="font-size: 1.3em;" aria-hidden="true"></i>&nbsp;<b>ถอนสิทธิ์ </b>&nbsp;','admin_delete',
                        [
                            'class' => 'input-group-text btn btn-outline-danger btn-sm',
                            'target'=>'',
                            'data'=>[
                                'method' => 'post',
                                'target'=>'_blank',
                                'params'=>[
                                    'User[ADMIN_ID]' => $data->ADMIN_ID,
                                ],
                            ],
                        ]) ?>
                </td>
            <?php }else{ ?>
                <td style="font-size:14px"></td>
            <?php } ?>

            <?php }}} ?>
        </tr>

        </tbody>
    </table>

    <?php
    function API_agency_name($LCT,$Token){
        $curl = curl_init();
        $ADMINToken = 'LCT='.$LCT;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/allproject_api/agency_name',
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
    ?>

    <!------------------------------------------------------------------ Popup Add Permission -------------------------------------------------------------------------------->
    <div id="contact-popup">
        <form class="contact-form" action="" id="contact-form"
              method="post" enctype="multipart/form-data">
            <h1>SETTING</h1>

            <div class="container btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-dark active" style="width: 50%">
                    <input type="radio" name="options" id="option1" autocomplete="off" checked onclick="option_his()"> <b>HIS</b>
                </label>
                <label class="btn btn-outline-dark" style="width: 50%">
                    <input type="radio" name="options" id="option2" autocomplete="off" onclick="option_idcard()"> <b>ID CARD</b>
                </label>
            </div>

            <br> <br>

            <?php $form = ActiveForm::begin(); ?>
            <div class="row" >
                <div class="form-group mb-12" id="Add_his" style="display:table;">
                    <?= $form->field($model, 'Permission_level')->dropdownList(['1' => 'SuperAdmin','2' => 'Admin']) ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true , 'id' => 'txtusername' , 'placeholder' => 'ชื่อผู้ใช้งาน' , 'maxlength' => '13' , 'autocomplete' => 'off',  'required' => true ]) ?>

                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'รหัสผ่าน','id'=>'txtpassword', 'required' => true ]) ?>

                    <?= $form->field($model, 'Showpassword')->checkbox(['checked' => false , 'onchange'=>"mouseDownchangepasswordtotext(this,txtpassword);"]) ?>

                    <div class="row">
                        <div class="col">
                            <?= Html::submitButton('<b>เพิ่มสิทธิ์ <i class="fas fa-user-plus" style="font-size:19px;"></i></b>', ['class' => 'input-group-text btn btn-outline-success btn-sm',  'name' => 'Check-button' ,'value'=>1 , 'style'=>'width: 100%;']) ?>
                        </div>
                        <div class="col">
                            <?= Html::submitButton('<b>Cancel</b>', ['class' => 'input-group-text btn btn-outline-danger btn-sm',  'name' => 'Check-button' ,'value'=>3 , 'style'=>'width: 100%;' , 'onClick'=>"Check_Cancel(txtpassword);"]) ?>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-12" id="Add_idcard" style="display:none;">
                    <?= $form->field($model, 'Permission_level2')->dropdownList(['1' => 'SuperAdmin','2' => 'Admin']) ?>

                    <?= $form->field($model, 'Idcard')->textInput(['autofocus' => true , 'id' => 'Idcard' , 'value' => '-' , 'placeholder' => 'ระบุ : เลขบัตรประชาชน' , 'maxlength' => '13' , 'autocomplete' => 'off' , 'required' => true , 'onkeypress'=>'return MyKeyUsername(event);' ])->label(false) ?>

                    <div class="row">
                        <div class="col">
                            <?= Html::submitButton('<b>เพิ่มสิทธิ์ <i class="fas fa-user-plus" style="font-size:19px;"></i></b>', ['class' => 'input-group-text btn btn-outline-success btn-sm',  'name' => 'Check-button' ,'value'=>2 , 'style'=>'width: 100%;']) ?>
                        </div>
                        <div class="col">
                            <?= Html::submitButton('<b>Cancel</b>', ['class' => 'input-group-text btn btn-outline-danger btn-sm',  'name' => 'Check-button' ,'value'=>3 , 'style'=>'width: 100%;' ,  'onClick'=>"Check_Cancel(txtpassword);"]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

        </form>
    </div>
</div>
    <!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>

    <script type="text/javascript">

        $(function(){
            //========================================รอโหลดหน้า===============================================
            $("#overlay").fadeOut();
            $(".main-contain").removeClass("main-contain");
            //===============================================================================================
        });

        function MyKeyUsername(e) {
            //===============================================================================================
            //ไม่ให้พิมตัวอักษร
            var vchar = String.fromCharCode(event.keyCode);
//        if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
            if ((vchar < '0' || vchar > '9')) return false;
            e.onKeyPress = vchar;
            //===============================================================================================
            var keynum;

            if (window.event) { // IE
                keynum = e.keyCode;
            } else if (e.which) { // Netscape/Firefox/Opera
                keynum = e.which;
            }
            //===============================================================================================
            //ทำให้สามารถกด ENTER
            if (keynum === 13) {
                $('#btnlogin').click();
            }
            //===============================================================================================
        }

        function mouseDownchangepasswordtotext(e, textbox) {
            //===============================================================================================
            //แสดง password
            if (e.checked === true) {
                textbox.type = "text"
            }
            else {
                textbox.type = "password"
            }
        }

        function CheckDate(target, source) {
            //========================================ซ่อนและแสดงรูป===============================================
            document.getElementById(target).innerHTML = document.getElementById(source).innerHTML;
            //===============================================================================================
        }

        function option_his(){
            document.getElementById('Add_his').style.display = 'table';
            document.getElementById('txtusername').value = "";
            document.getElementById('txtpassword').value = "";

            document.getElementById('Add_idcard').style.display = 'none';
            document.getElementById('Idcard').value = "-";
        }

        function option_idcard(){
            document.getElementById('Add_idcard').style.display = 'table';
            document.getElementById('txtusername').value = "-";
            document.getElementById('txtpassword').value = "-";

            document.getElementById('Add_his').style.display = 'none';
            document.getElementById('Idcard').value = "";
        }

        function Check_Cancel(textbox){
            textbox.type = "text";
            document.getElementById('txtusername').value = " ";
            document.getElementById('txtpassword').value = " ";
            document.getElementById('Idcard').value = " ";
        }

        $(document).ready(function () {//Popup Add Permission
            $("#contact-icon").click(function () {
                $("#contact-popup").show();
            });
            $("#Check-Cancel").click(function () {
                $("#contact-popup").hide();
            });
        });

    </script>

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

        #contact-popup {
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
            width: 350px;
            margin: 0px;
            background-color: white;
            font-family: Arial;
            position: relative;
            left: 50%;
            top: 50%;
            margin-left: -210px;
            margin-top: -255px;
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

        #contact-popup h1 {
            font-weight: normal;
            text-align: center;
            margin: 10px 0px 20px 0px;
        }

        .input-error {
            border: #e66262 1px solid;
        }
        /* ------------------------------------------------------------------------------------------------------------------------------------- */


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
        .topnav {
            overflow: hidden;
            background-color: #e9e9e9;
        }

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