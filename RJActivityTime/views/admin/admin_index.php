<?php

/* @var $this yii\web\View */
use kartik\select2\Select2;
use common\widgets\Alert;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

$this->title = 'My Yii Application';

?>
<header>

    <?php
    NavBar::begin([
        'brandLabel' => 'RJ Activity Time Admin  (ระบบแสดงหน้าจอการจัดการปฏิทินกิจกรรม)',
        'brandUrl' => ['/admin/admin_index'],
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    $menuItems = [
//        ['label' => 'Home', 'url' => ['/admin/admin_index']],
        ['label' => 'Logout', 'url' => ['/login/login_his']],
    ];
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

</header>

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
                <span id="replace_target"><img src="https://rjhome.rajavithi.go.th/RJActivityTime/images/Loading.gif" style="width: 250px; height: 70px"></span>
            </div>
            <!--------------------------------------------------------------------------------------------------Alert------------------------------------------------------------------------------------------------------------------------------>
            <br><?= Alert::widget() ?><br>
        </div>
        <?php if (isset($API)) { ?>
            <b>&nbsp;&nbsp; จำนวน :&nbsp; <?php echo $API->json_total; ?> &nbsp; รายการ </b>
        <?php } ?>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="float-right">
        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myPopUpInsert"><b style="color: black">เพิ่มข้อมูลกิจกรรม +</b></button>
    </div>
    <div class="modal fade" id="myPopUpInsert" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width: 100%">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: black">รายละเอียดกิจกรรม</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="activity_title"  style="color: black; text-align: right;"><b>หัวข้อกิจกรรม</b></label>
                            <input type="text" class="form-control" id="activity_title" autocomplete=off>
                        </div>
                        <div class="form-group">
                            <label for="description"  style="color: black; text-align: right;"><b>รายละเอียด</b></label>
                            <textarea class="form-control" id="description" rows="4" cols="50"></textarea>
                        </div>
                        <div class="form-group">
                            <label>
                                <div class="input-group-prepend">
                                    <span class="input-group-text btn btn-info" >วันที่เริ่มกิจกรรม&nbsp;&nbsp;</span>
                                </div>
                            </label>
                            <label>
                                <?php
                                // Highlight today, show today button, change date format (use convertFormat
                                // to auto convert PHP DateTime Format to DateTimePicker format).
                                echo DateTimePicker::widget([
                                    'name' => 'startdate',
                                    'id' => 'startdate',
                                    'options' => [
                                        'placeholder' => 'Start event time ...',
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
                        </div>
                        <div class="form-group">
                            <label>
                                <div class="input-group-prepend">
                                    <span class="input-group-text btn btn-info" > สิ้นสุดกิจกรรม&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	</span>
                                </div>
                            </label>
                            <label>
                                <?php
                                // Highlight today, show today button, change date format (use convertFormat
                                // to auto convert PHP DateTime Format to DateTimePicker format).
                                echo DateTimePicker::widget([
                                    'name' => 'enddate',
                                    'id' => 'enddate',
                                    'options' => [
                                        'placeholder' => 'End event time ...',
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
                        </div>
                        <div class="form-group">
                            <label>
                                <div class="input-group-prepend">
                                    <span class="input-group-text btn btn-info" > สีพื้นหลังกิจกรรม	</span>
                                </div>
                            </label>
                            <select name="bgcolor" id="bgcolor">
                                <option class="dropdown-menu" value="#4cbfff" style="background-color:#4cbfff">Blue</option>
                                <option value="#ff5351" style="background-color:#ff5351;color:#ff5351;">Red</option>
                                <option value="#eacb00" style="background-color:#eacb00;color:#eacb00;">Yellow</option>
                                <option value="#00aa00" style="background-color:#00aa00;color:#00aa00;">Green</option>
                                <option value="#f0802c" style="background-color:#f0802c;color:#f0802c;">Orange</option>
                                <option value="#e75bf0" style="background-color:#e75bf0;color:#e75bf0;">Pink</option>
                                <option value="#6e12cd" style="background-color:#6e12cd;color:#6e12cd;">Purple</option>
                                <option value="#000000" style="background-color:#000000;color:#000000;">Black</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="link"  style="color: black; text-align: right;"><b>Link</b></label>
                            <input type="text" class="form-control" id="link">
                        </div>
                    </form>
                </div>

                <div class="modal-footer" id="Submit_Succes">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="Insert_event(
                        document.getElementById('activity_title').value,
                        document.getElementById('description').value,
                        document.getElementById('startdate').value,
                        document.getElementById('enddate').value,
                        document.getElementById('bgcolor').value,
                        document.getElementById('link').value,
                        )">ตกลง</button>
                </div>
            </div>
        </div>
    </div>
    <br>
        <?php if (isset($API)&&isset($dateS)&&isset($dateE)) { ?>
            <div  id="Box_Incident_Rmgroup" >
                <?php
                echo $this->render('_Incident_cencel', [
                    'model' => $model,
                    'API' => $API,
                    'dateS' => $dateS,
                    'dateE' => $dateE,
                ]) ;
                ?>
            </div>
        <?php } ?>

</div>


<script type="text/javascript">

    function CheckDate(target, source) {
        //========================================ซ่อนและแสดงรูป===============================================
        document.getElementById(target).innerHTML = document.getElementById(source).innerHTML;
        //===============================================================================================
    }

    function Cancel_event(data,date){
        var ACTIVITY_TITLE = date.split('-');
        var text = "คุณต้องการยกเลิกรายการลำดับที่ "+ACTIVITY_TITLE[2]+" ใช่หรือไม่"+"\n\n- กรณียกเลิกแล้วจะไม่สามารถกลับมาแก้ไขได้อีก"+"\n- กรณีแก้ไขข้อมูลให้เลือกที่ 'แก้ไขรายการกิจกรรม'";
        if (confirm(text) === true) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['admin/cancelevent']) ?>',
                type: 'POST',
                data: 'Data_ACTIVITY_ID='+data+'&Data_date='+date,
                success: function (data) {
                    $('#Box_Incident_Rmgroup').html(data);
                }
            });
        } else {
            text = "You canceled!";
        }
        document.getElementById("demo").innerHTML = text;
    }

    function reload(){
        window.location.reload();
    }

    function Updata_event(activitytitle,description,startdate,enddate,bgcolor,link,activity_id,date){
//        alert(description.length);
        var text = "คุณต้องการแก้ไขข้อมูล ใช่หรือไม่";
        var date1 =  startdate.split('/');
        var date2 = enddate.split('/');
        var time1 =  date1[2].split(' ');
        var time2 =  date2[2].split(' ');
        if(time1[0] <= time2[0]){//Year
            if(date1[1] <= date2[1]){//Month
                if(date1[0] <= date2[0]){//day
                    if(date1[0] !== date2[0]){
                        if (confirm(text) === true) {
                            $.ajax({
                                url: '<?= \yii\helpers\Url::to(['admin/updateevent']) ?>',
                                type: 'POST',
                                data: 'Data_Title='+activitytitle+'&Data_date='+description+'&Startdate='+startdate+'&Enddate='+enddate+'&Bgcolor='+bgcolor+'&Link='+link+'&Activity_id='+activity_id+'&DateA='+date,
                                success: function (data) {
                                    $('#Box_Incident_Rmgroup').html(data);
                                }
                            });
                        } else {
                            text = "You canceled!";
                        }
                        document.getElementById("demo").innerHTML = text;
                    }else {
                        if(time1[1] <= time2[1]){
                            if (confirm(text) === true) {
                                $.ajax({
                                    url: '<?= \yii\helpers\Url::to(['admin/updateevent']) ?>',
                                    type: 'POST',
                                    data: 'Data_Title='+activitytitle+'&Data_date='+description+'&Startdate='+startdate+'&Enddate='+enddate+'&Bgcolor='+bgcolor+'&Link='+link+'&Activity_id='+activity_id+'&DateA='+date,
                                    success: function (data) {
                                        $('#Box_Incident_Rmgroup').html(data);
                                    }
                                });
                            } else {
                                text = "You canceled!";
                            }
                            document.getElementById("demo").innerHTML = text;
                        }else {
                            alert('เวลาสุดกิจกรรม ต้องมากกว่าหรือเท่ากับ เวลาที่เริ่มกิจกรรมได้');
                        }
                    }
                }else {
                    alert('วันที่สิ้นสุดกิจกรรม ต้องมากกว่าหรือเท่ากับ วันที่เริ่มกิจกรรมได้');
                }
            }else {
                alert('เดือนที่สิ้นสุดกิจกรรม ต้องมากกว่าหรือเท่ากับ เดือนที่เริ่มกิจกรรมได้');
            }
        }else {
            alert('ปีที่สิ้นสุดกิจกรรม ต้องมากกว่าหรือเท่ากับ ปีที่เริ่มกิจกรรมได้');
        }
    }

    function Insert_event(activitytitle,description,startdate,enddate,bgcolor,link){
        var text = "คุณต้องการเพิ่มข้อมูลกิจกรรม ใช่หรือไม่";
        var date1 =  startdate.split('/');
        var date2 = enddate.split('/');
        var time1 =  date1[2].split(' ');
        var time2 =  date2[2].split(' ');
        if(time1[0] <= time2[0]){//Year
            if(date1[1] <= date2[1]){//Month
                if(date1[0] <= date2[0]){//day
                    if(date1[0] !== date2[0]){
                        if (confirm(text) === true) {
                            $.ajax({
                                url: '<?= \yii\helpers\Url::to(['admin/insertevent']) ?>',
                                type: 'POST',
                                data: 'Data_Title='+activitytitle+'&Data_date='+description+'&Startdate='+startdate+'&Enddate='+enddate+'&Bgcolor='+bgcolor+'&Link='+link,
                                success: function (data) {
                                    $('#Box_Incident_Rmgroup').html(data);
                                }
                            });
                        } else {
                            text = "You canceled!";
                        }
                        document.getElementById("demo").innerHTML = text;
                        window.location.reload();
                    }else {
                        if(time1[1] <= time2[1]){
                            if (confirm(text) === true) {
                                $.ajax({
                                    url: '<?= \yii\helpers\Url::to(['admin/insertevent']) ?>',
                                    type: 'POST',
                                    data: 'Data_Title='+activitytitle+'&Data_date='+description+'&Startdate='+startdate+'&Enddate='+enddate+'&Bgcolor='+bgcolor+'&Link='+link,
                                    success: function (data) {
                                        $('#Box_Incident_Rmgroup').html(data);
                                    }
                                });
                            } else {
                                text = "You canceled!";
                            }
                            document.getElementById("demo").innerHTML = text;
                            window.location.reload();
                        }else {
                            alert('เวลาสุดกิจกรรม ต้องมากกว่าหรือเท่ากับ เวลาที่เริ่มกิจกรรมได้');
                        }
                    }
                }else {
                    alert('วันที่สิ้นสุดกิจกรรม ต้องมากกว่าหรือเท่ากับ วันที่เริ่มกิจกรรมได้');
                }
            }else {
                alert('เดือนที่สิ้นสุดกิจกรรม ต้องมากกว่าหรือเท่ากับ เดือนที่เริ่มกิจกรรมได้');
            }
        }else {
            alert('ปีที่สิ้นสุดกิจกรรม ต้องมากกว่าหรือเท่ากับ ปีที่เริ่มกิจกรรมได้');
        }
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