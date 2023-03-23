<?php

/* @var $this yii\web\View */
use common\widgets\Alert;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;


$this->title = 'My Yii Application';

?>



<div class="site-index">

    <!--     on your view layout file HEAD section -->
    <link rel="stylesheet" href="<?=\yii\helpers\Url::to('@web/../RJESI/css/all.css'); ?>">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <!--     on your view layout file HEAD section-->
    <script defer src="<?=\yii\helpers\Url::to('@web/../RJESI/js/all.js'); ?>" crossorigin="anonymous"></script>
    <br><br>

    <div class="row">
        <div class="form-group mb-12">
            <?php $form = ActiveForm::begin(); ?>

<!----------------------------------------------------------------------------------------DateRangePicker----------------------------------------------------------------------------------------------------------------------->
            <div class="container">
                <?= $form->field($model, 'Statuser')->dropdownList(['0' => 'All Status','1' => 'SAME','2' => 'DIFFERENT', '3' => 'OVER', '4' => 'UNDER'])->label(false)?>
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
                    <span id="replace_target"><img src="https://rjhome.rajavithi.go.th/RJESI/images/Loading.gif" style="width: 250px; height: 70px"></span>
                </div>
<!--------------------------------------------------------------------------------------------------Alert------------------------------------------------------------------------------------------------------------------------------>
                <br><?= Alert::widget() ?><br>
<!-----------------------------------------------------------------------------------------เช็คจำนวน OVER UNDER---------------------------------------------------------------------------------------------------------------->
            <?php $SelectedDate = $model->SelectedDate;
            if (isset($SUMDayCheck) && isset($SDateThai) && isset($EDateThai) && isset($API)&& isset($CountSAME) && isset($CountOVER) && isset($CountUNDER)) {
                if($SelectedDate != "" && $SUMDayCheck <= 30 ){
                    ?>

                    <div  id="replace_target" class="card container" style="width: 50%;">
                        <ul class="list-group list-group-flush">

                            <li class="list-group-item"><b>วันที่ :&nbsp;&nbsp;</b>
                                <?php echo $SDateThai."<b>&nbsp;&nbsp;ถึง&nbsp;&nbsp;</b>".$EDateThai; ?>
                                <b>&nbsp;&nbsp; จำนวน :&nbsp; <?php echo $API->json_total; ?> &nbsp; รายการ </b>
                                <div class="progress">
                                    <div class="progress-bar" style="background-color: #56cc9d; width:<?php echo  number_format(($CountSAME*100)/$API->json_total , 2)?>%">
                                        SAME
                                    </div>
                                    <div class="progress-bar" style="background-color: #7c6fdc; width:<?php echo  number_format(($CountOVER*100)/$API->json_total , 2)?>%">
                                        OVER
                                    </div>
                                    <div class="progress-bar" style="background-color: #cc1f00; width:<?php echo  number_format(($CountUNDER*100)/$API->json_total , 2)?>%">
                                        UNDER
                                    </div>
                                </div>
                            </li>

                            <li class="list-group-item"><b style="color: #56cc9d">SAME จำนวน :&nbsp;&nbsp;<?php echo $CountSAME ; ?>&nbsp;&nbsp; รายการ </b>
                                <div class="progress">
                                    <div class="progress-bar" style="background-color: #56cc9d; width:<?php echo  number_format(($CountSAME*100)/$API->json_total  , 2)?>%"><b><?php echo  number_format(($CountSAME*100)/$API->json_total  , 2)?>%</b></div>
                                </div>
                            </li>
                            <li class="list-group-item"><b style="color: #7c6fdc">OVER จำนวน :&nbsp;&nbsp;<?php echo $CountOVER ; ?>&nbsp;&nbsp; รายการ </b>
                                <div class="progress">
                                    <div class="progress-bar" style="background-color: #7c6fdc; width:<?php echo  number_format(($CountOVER*100)/$API->json_total  , 2)?>%"><b><?php echo  number_format(($CountOVER*100)/$API->json_total  , 2)?>%</b></div>
                                </div>
                            </li>
                            <li class="list-group-item"><b style="color: #cc1f00">UNDER จำนวน :&nbsp;&nbsp;<?php echo $CountUNDER ; ?>&nbsp;&nbsp; รายการ </b>
                                <div class="progress">
                                    <div class="progress-bar" style="background-color: #cc1f00; width:<?php echo  number_format(($CountUNDER*100)/$API->json_total  , 2)?>%"><b><?php echo  number_format(($CountUNDER*100)/$API->json_total  , 2)?>%</b></div>
                                </div>
                            </li>

                            <li class="list-group-item">   <?= Html::submitButton('Download Excel', ['class' => 'btn btn-dark btn-block', 'name' => 'Check-button','value'=>2]) ?></li>
                        </ul>
                    </div>
                <?php }
            } ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <table>
        <tbody>
        <tr style="border: 2px solid #000000; background: -webkit-linear-gradient(45deg, #5bccc8,#87dfdc); font-size:15px">
            <th style="color:black" width="100 px">HN</th>
            <th style="color:black" width="150 px">วันส่งตรวจ</th>
            <th style="color:black" width="250 px">ชื่อคนไข้</th>
            <th style="color:black" width="150 px">เวลาซักประวัติ</th>
            <th style="color:black" width="150 px">สถานะสุดท้าย</th>
            <th style="color:black" width="100 px">Trauma</th>
            <th style="color:black" width="80 px">ESI DOC</th>
            <th style="color:black" width="250 px">ชื่อ แพทย์</th>
            <th style="color:black" width="80 px">ESI</th>
            <th style="color:black" width="150 px">Chief Complaint</th>
            <th style="color:black; border-left: 2px solid #000000; border-right: 2px solid #000000;" width="50 px">Status</th>
            <th style="color:black" colspan="2">BP</th>
            <th style="color:black" width="50 px">T</th>
            <th style="color:black" width="50 px">P</th>
            <th style="color:black" width="50 px">R</th>
        </tr><br>

        <?php
        if (isset($API)){
        if($API->json_result == true ){
            foreach ($API->json_data as $data){ //วน tr
            ?>
            <tr onMouseover="this.style.backgroundColor='#343434';  this.style.color = 'white'; " onMouseout="this.style.backgroundColor='';  this.style.color = '';">
                <td style="font-size:14px"><?php echo $data->HN;?> </td>
                <td style="font-size:14px"><?php echo $data->DDATE." ".$data->DTIME;?> </td>
                <td style="font-size:14px"><?php echo $data->PNAME;?> </td>
                <td style="font-size:14px"><?php echo $data->HDATE." ".$data->HTIME;?> </td>
                <td style="font-size:14px"><?php echo $data->FSTATUS;?> </td>
                <td style="font-size:14px"><?php echo $data->TRAUMA;?> </td>
                <td style="font-size:14px"><?php echo $data->ESI_DOC;?> </td>
                <td style="font-size:14px"><?php echo $data->MEDICO;?> </td>
                <td style="font-size:14px"><?php echo $data->ESI;?> </td>
                <td style="font-size:14px"><?php echo $data->CHIEFCOMPLAINT;?> </td>

                <?php if( $data->ST == "OVER"){ ?>
                    <td style="font-size:14px; border-left: 2px solid #000000; border-right: 2px solid #000000; color:black; background: #9d90dc;"><b><?php echo $data->ST;?></b></td>
                <?php }else if($data->ST == "SAME"){ ?>
                    <td style="font-size:14px; border-left: 2px solid #000000; border-right: 2px solid #000000; color:black; background: #80dcd9;"><b><?php echo $data->ST;?></b></td>
                <?php }else{ ?>
                    <td style="font-size:14px; border-left: 2px solid #000000; border-right: 2px solid #000000; color:black; background: #f3969a;"><b><?php echo $data->ST;?></b></td>
                <?php } ?>

                <td width="50 px" style="font-size:14px"><?php echo $data->HBPN;?> </td>
                <td width="50 px" style="font-size:14px"><?php echo $data->LBPN;?> </td>
                <td style="font-size:14px"><?php echo $data->BT;?> </td>
                <td style="font-size:14px"><?php echo $data->PR;?> </td>
                <td style="font-size:14px"><?php echo $data->RR;?> </td>

                <?php }
            }
        } ?>
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