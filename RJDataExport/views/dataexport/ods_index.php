<?php

/* @var $this yii\web\View */
use kartik\select2\Select2;
use common\widgets\Alert;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\helpers\Url;



$this->title = 'My Yii Application';

?>


<div class="site-index">
    <!--     on your view layout file HEAD section -->
    <link rel="stylesheet" href="<?=\yii\helpers\Url::to('@web/../RJESI/css/all.css'); ?>">
    <!--     on your view layout file HEAD section-->
    <script defer src="<?=\yii\helpers\Url::to('@web/../RJESI/js/all.js'); ?>" crossorigin="anonymous"></script>
    <br><br>

    <div class="row">
        <div class="form-group mb-12">

            <?php $form = ActiveForm::begin(); ?>

            <!----------------------------------------------------------------------------------------DateRangePicker----------------------------------------------------------------------------------------------------------------------->
            <div class="container">

                <div class="row" style="padding:5px;">
                    <div class="col-md-6">
                        <?php
                            // DateRangePicker in a dropdown format (uneditable/hidden input) and uses the preset dropdown.
                            // Note that placeholder setting in this case will be displayed when value is null
                            // Also the includeMonthsFilter setting will display LAST 3, 6 and 12 MONTHS filters.
                            echo '<div class="drp-container">';
                            echo '<label class="control-label" style="color: black"><b>วันที่ตรวจ</b></label>';
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
                    </div>
                    <div class="col-md-6">
                        <?php
                            //check_pttype_name
                            if (isset($Array_pttype)) {
                                echo '<label class="control-label" style="color: black"><b>สิทธิ์การรักษา</b></label>';
                                echo Select2::widget([
                                    'name' => 'Pttype_ID',
                                    'id' => 'Pttype_ID',
                                    'value' => Yii::$app->request->post('Pttype_ID'), // initial value
                                    'data' => $Array_pttype,
                                    'options' => [
                                        'placeholder' => 'สิทธิ์การรักษา ...',
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                ]);
                            }
                        ?>
                    </div>
                </div>
            </div><br>

            <?php if (isset($API)) {
                $SelectedDate = $model->SelectedDate;
                if(Yii::$app->request->post('Pttype_ID') == '7085' && $SelectedDate != "" && $API->json_data != []){ ?>
                    <div class="container">
                        <div class="row" style="padding:5px;">
                            <div class="col-md-6">
                                <!-----------------------------------------------------------------------------------------------Button----------------------------------------------------------------------------------------------------------------------------->
                                <div class="container">
                                    <?= Html::submitButton('Check', ['class' => 'btn btn-dark btn-block', 'name' => 'Check-button', 'value'=>1, 'onClick'=>"CheckDate('target', 'replace_target')"]) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-----------------------------------------------------------------------------------------------Button----------------------------------------------------------------------------------------------------------------------------->
                                <div class="container">
                                    <?= Html::submitButton('Download File', ['class' => 'btn btn-dark btn-block', 'name' => 'Check-button', 'value'=>0, 'onClick'=>"CheckDate('target', 'replace_target')"]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else{ ?>
                    <div class="container">
                        <?= Html::submitButton('Check', ['class' => 'btn btn-dark btn-block', 'name' => 'Check-button', 'value'=>1, 'onClick'=>"CheckDate('target', 'replace_target')"]) ?>
                    </div>
                <?php }
            }else{ ?>
                <div class="container">
                    <?= Html::submitButton('Check', ['class' => 'btn btn-dark btn-block', 'name' => 'Check-button', 'value'=>1, 'onClick'=>"CheckDate('target', 'replace_target')"]) ?>
                </div>
            <?php } ?>

            <!-----------------------------------------------------------------------------------------------Loading---------------------------------------------------------------------------------------------------------------------------->
            <div>
                <span id="target"></span>
            </div>
            <div class="row" style="display:none">
                <span id="replace_target"><img src="https://rjhome.rajavithi.go.th/RJESI/images/Loading.gif" style="width: 250px; height: 70px"></span>
            </div>
            <!--------------------------------------------------------------------------------------------------Alert------------------------------------------------------------------------------------------------------------------------------>
            <br><?= Alert::widget() ?><br>

            <?php if (isset($API)) { ?>
                <b>&nbsp;&nbsp; จำนวน :&nbsp; <?php echo $API->json_total; ?> &nbsp; รายการ </b>
            <?php } ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>


    <table >
        <tbody >
        <tr style="border: 2px solid #000000; background: -webkit-linear-gradient(45deg, #f6871d,#ff8820); font-size:15px">
            <th style="color:black" width="50 px">ลำดับ</th>
            <th style="color:black" width="100 px">HN</th>
            <th style="color:black" width="100 px">AN</th>
            <th style="color:black" width="250 px">ชื่อ-สกุล</th>
            <th style="color:black" width="110 px">วันที่รับรักษา</th>
            <th style="color:black" width="110 px">วันที่จำหน่าย</th>
            <th style="color:black" width="100 px">เลขที่อ้างอิง</th>
            <th style="color:black" width="170 px">ค่าใช้จ่ายภายใน ร.พ.</th>
            <th style="color:black" width="150 px">จำนวนเรียกเก็บ</th>
            <th style="color:black" width="170 px">ประเภทสิทการรักษา</th>
            <th style="color:black" width="170 px">หน่วยงานที่เรียกเก็บ</th>
            <th style="color:black" width="170 px">รายงาน</th>
        </tr><br>

        <?php
            $number_hn = 1;
            if (isset($API)){
            if($API->json_result == true ){
            foreach ($API->json_data as $data){ //วน tr

            $IDATE = explode(" ", $data->INCDATE);
            if ($IDATE[1] == "ม.ค.") {
                $IMONTH = "01";
            } else if ($IDATE[1] == "ก.พ.") {
                $IMONTH = "02";
            } else if ($IDATE[1] == "มี.ค.") {
                $IMONTH = "03";
            } else if ($IDATE[1] == "เม.ย.") {
                $IMONTH = "04";
            } else if ($IDATE[1] == "พ.ค.") {
                $IMONTH = "05";
            } else if ($IDATE[1] == "มิ.ย.") {
                $IMONTH = "06";
            } else if ($IDATE[1] == "ก.ค.") {
                $IMONTH = "07";
            } else if ($IDATE[1] == "ส.ค.") {
                $IMONTH = "08";
            } else if ($IDATE[1] == "ก.ย.") {
                $IMONTH = "09";
            } else if ($IDATE[1] == "ต.ค.") {
                $IMONTH = "10";
            } else if ($IDATE[1] == "พ.ย.") {
                $IMONTH = "11";
            } else if ($IDATE[1] == "ธ.ค.") {
                $IMONTH = "12";
            }

        if (isset($IMONTH)) {
            if(strlen($IDATE[0]) == 2){
                $TDATE_0 =  $IDATE[0] ;
                $IDATETHV = strval($TDATE_0 . $IMONTH . $IDATE[2] - 543);
            }else{
                $IDATETHVSET = strval($IDATE[0] . $IMONTH . $IDATE[2] - 543);
                $IDATETHV = "0".$IDATETHVSET;
            }

            $curl = curl_init();
            $DataToken = 'SDATE=' . $IDATETHV . '&EDATE=' . $IDATETHV . '&PTTYPE=' .  $data->PTTYPE . '&HNID=' . $data->HN;
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjdataexport_api/ods_uc_permissions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $DataToken,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . Yii::$app->session->get('access_token'),
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));

            $response = curl_exec($curl);
            $ods_uc_permissions = json_decode($response);
            $data_ods = $ods_uc_permissions->json_data;

        ?>

        <?php if($data_ods == []){ ?>
            <!-- ไม่ได้มาจากคลีนิค ODS MIS อยู่หน่วยงานอื่น -->
        <?php }else{ ?>
        <tr onMouseover="this.style.backgroundColor='#343434';  this.style.color = 'white'; " onMouseout="this.style.backgroundColor='';  this.style.color = '';">

            <td class="text-nowrap" style="font-size:14px;"><b><?php echo $number_hn;?></b></td>
            <td class="text-nowrap" style="font-size:14px;"><?php echo $data->HN;?></td>
            <td class="text-nowrap" style="font-size:14px;">ODS????</b></td>
            <td class="text-nowrap" style="font-size:14px;"><?php echo $data->DSPNAMEPT;?></td>
            <td class="text-nowrap" style="font-size:14px;"><?php echo $data->INCDATE;?></td>
            <td class="text-nowrap" style="font-size:14px;"><?php echo $data->MAXDATE;?></td>
            <td class="text-nowrap" style="font-size:14px;"><?php echo $data->ARNO;?></td>
            <td class="text-nowrap" style="font-size:14px;"><b><?php echo number_format($data->S_INCPT, 2); ?></b></td>
            <td class="text-nowrap" style="font-size:14px;"><b><?php echo number_format($data->S_ARPT, 2); ?></b></td>
            <td class="text-nowrap" style="font-size:14px;"><b><?php echo $data->PNAME;?></b></td>
            <td class="text-nowrap" style="font-size:14px;"><?php echo $data->NAME;?></td>

            <td class="text-nowrap" style="font-size:14px">
                <?= Html::a('&nbsp;<i class="fa fa-print"  style="font-size: 1.3em;color: #e5e5e5" aria-hidden="true"></i>&nbsp;<b style="color: black">สรุปรายงาน</b>&nbsp;','fpdfreport',
                    [
                        'class' => 'btn btn-danger btn-sm',
                        'target'=>'_blank',
                        'data'=>[
                            'method' => 'post',
                            'target'=>'_blank',
                            'params'=>[
                                'User[HN]' => $data_ods[0]->HN,
                                'User[NAME]' => $data_ods[0]->HNNAME,
                                'User[BRTHDATE]' => $data_ods[0]->BRTHDATE,
                                'User[CARDNO]' => $data_ods[0]->CARDNO,
                                'User[AGE]' => $data_ods[0]->AGE,
                                'User[MALEN]' => $data_ods[0]->MALEN,
                                'User[OCCPTN]' => $data_ods[0]->OCCPTN,
                                'User[NTNLTYN]' => $data_ods[0]->NTNLTYN,
                                'User[MRTLST]' => $data_ods[0]->MRTLST,
                                'User[CLAIMLCT]' => $data_ods[0]->CLAIMLCT,
                                'User[CLAIMLCTN]' => $data_ods[0]->CLAIMLCTN,
                                'User[CLINICLCT]' => $data_ods[0]->CLINICLCT,
                                'User[DDATE]' => $data_ods[0]->DDATE." "."(".$data_ods[0]->DTIME.")",
                                'User[PTTYPE1]' => $data_ods[0]->PTTYPE1,
                                'User[PTTYPE_NM]' => $data_ods[0]->PTTYPE_NM,
                            ],
                        ],
                    ]) ?>
                <?php  ++$number_hn; } ?>
            </td>
            <?php }}}} ?>
        </tr>
        </tbody>
    </table>

</div>





<script type="text/javascript">

    function CheckDate(target, source) {
        //========================================ซ่อนและแสดงรูป===============================================
        document.getElementById(target).innerHTML = document.getElementById(source).innerHTML;
        //===============================================================================================
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