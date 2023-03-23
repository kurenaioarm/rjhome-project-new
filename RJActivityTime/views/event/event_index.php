<?php

/* @var $this yii\web\View */
use common\widgets\Alert;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;


$this->title = 'My Yii Application';

?>


<div class="row">
    <b style="font-size: 23px;display:none;" id="Tiele_event" >ปฏิทินกิจกรรม</b>
    <div class="col-7">
        <hr>
        <div class="form-group mb-12 " style="color:#24666f;width:100%;height: 100%;">
            <?= \yii2fullcalendar\yii2fullcalendar::widget(array(
                'events'=> $events,
                'header' => [
                    'left'=>'prev,next, today',//prev,next, today
                    'center'=> 'title',
                    'right'=>'today ,prev,next',//month,agendaWeek,agendaDay
                ],
                'options' => [
                    'lang' => 'us',
                ],
                'clientOptions' => [
                    'aspectRatio'       => 1.4,//ขนาด
//                                'editable' => true,//ขยับ EVENT
                    'draggable' => true,
                    'droppable' => true,
                    'theme' => false,//สีหัวตราง
                    'selectable' => true,//สีที่ช่อง
                    'weekends' => true ,
                    //==================================== Check Day ================================================
                    'select' => new JSExpression("function(date, allDay, jsEvent, view) {
                                     Search_DayEvent(date);
                                }"),
                    //==================================== Check Event ================================================
                    'eventClick' => new JsExpression("function(event, jsEvent, view) {
                                      Search_Event(event.id);
                                }"),
                    //===============================================================================================
                ],
            )); ?>
        </div>
    </div>
    <div class="col-5" id="Box_Data_Eventgroup">
        <?php
        echo $this->render('_Incident_Eventgroup', [
            'Data_date_event' =>[],
        ]) ;
        ?>
    </div>
</div>




<script type="text/javascript">

    document.addEventListener('contextmenu', event => event.preventDefault());

    function Search_DayEvent(data_day){
//        var Weekend = data_day.toString().split(' ');
        document.getElementById('Tiele_event').style.display = 'table';
        var String_day = data_day.toString() ; //แปลงเป็น String
        var Data_Date = String_day.split(' ');
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['event/eventday']) ?>',
            type: 'POST',
            data: 'Name_day='+Data_Date[0]+'&Name_month='+Data_Date[1]+'&Day='+Data_Date[2]+'&Year='+Data_Date[3],
            success: function (data) {
                $('#Box_Data_Eventgroup').html(data);
            }
        });
    }

    function Search_Event(event){
        document.getElementById('Tiele_event').style.display = 'table';
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['event/event']) ?>',
            type: 'POST',
            data: 'Even_id='+event,
            success: function (data) {
                $('#Box_Data_Eventgroup').html(data);
            }
        });
    }

    function CheckDate(target, source) {
        //========================================ซ่อนและแสดงรูป===============================================
        document.getElementById(target).innerHTML = document.getElementById(source).innerHTML;
        //===============================================================================================
    }

</script>

<style>
    .fc-sun {
        color: #000378;
        border-color: black;
        background-color: #fdddd8;
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