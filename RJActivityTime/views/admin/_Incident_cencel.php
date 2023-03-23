<?php
use common\widgets\Alert;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
?>

<?php
$number_hn = 1;
if (isset($API)&&isset($dateS)&&isset($dateE)){
?>
<table>
    <tbody >
    <tr style="border: 2px solid #000000; background: -webkit-linear-gradient(45deg, #f3969a,#f3969a); font-size:15px">
        <th style="color:black" width="50 px">ลำดับ</th>
        <th style="color:black" width="550 px">กิจกรรม</th>
        <th style="color:black" width="80 px">วันที่เริ่มกิจกรรม</th>
        <th style="color:black" width="80 px">สิ้นสุดกิจกรรม</th>
        <th style="color:black" width="80 px">วันที่เแก้ไขข้อมูล</th>
        <th style="color:black" width="50 px">สถานะ</th>
        <th style="color:black" width="120 px">ยกเลิกกิจกรรม</th>
        <th style="color:black" width="120 px">รายละเอียด / แก้ไข</th>
    </tr><br>


    <?php  if($API->json_result == true ){
    foreach ($API->json_data as $data){ //วน tr
    ?>
    <tr onMouseover="this.style.backgroundColor='#343434';  this.style.color = 'white'; " onMouseout="this.style.backgroundColor='';  this.style.color = '';">
        <td style="font-size:14px;"><b><?php echo $number_hn;?></b></td>
        <td style="font-size:14px;text-align:left;">&nbsp;&nbsp;&nbsp;<b><?php echo $data->ACTIVITY_TITLE;?> <?= Html::a('<b style="color: #ff5351;"><i class="fa fa-search-plus" aria-hidden="true"></i></b>','http://rjhome.com/RJActivityTime/index.php/event/event_index',[ 'class' => 'btn btn-outline-light btn-sm','target'=>'_blank' ]) ?>     </td>
        <td style="font-size:14px;"><?php echo $data->STARTDATE;?></td>
        <td style="font-size:14px;"><?php echo $data->ENDDATE;?></td>
        <td style="font-size:14px; color: #ff5351; "><b><?php echo $data->FIRSTDATE;?></b></td>
        <?php if($data->CANCELDATE == null){ ?>
            <td style="font-size:14px;color:black; background: #5cffa6;"><b>Active</b></td>
            <td style="font-size:14px">
                <!-----------------------------------------------------------------------------------------------Button----------------------------------------------------------------------------------------------------------------------------->
                <div class="container">
                    <?= Html::submitButton('<b>ยกเลิก</b>', ['class' => 'btn btn-light btn-block', 'style' => 'color:black;background-color: #ff4f57;', 'value'=> $dateS."-".$dateE."-".$number_hn , 'name' => 'Check-buttonCancel', 'onClick'=>"Cancel_event($data->ACTIVITY_ID,this.value)"]) ?>
                </div>
            </td>
            <td style="font-size:14px">
                <!-----------------------------------------------------------------------------------------------Button----------------------------------------------------------------------------------------------------------------------------->
                <div class="container">
                    <button type="button" class="btn btn-light btn-block" style="color:black;background-color: #53cb7b;" data-toggle="modal" data-target="#myPopUp-<?php echo $data->ACTIVITY_ID ?>"><b>ดูข้อมูล / แก้ไข</b></button>
                </div>
            </td>

            <div class="modal fade" id="myPopUp-<?php echo $data->ACTIVITY_ID ?>" tabindex="-1" role="dialog" aria-hidden="true" >
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
                                    <label for="activity_title-<?php echo $data->ACTIVITY_ID ?>"  style="color: black; text-align: right;"><b>หัวข้อกิจกรรม</b></label>
                                    <input type="text" class="form-control" id="activity_title-<?php echo $data->ACTIVITY_ID ?>" value="<?php echo $data->ACTIVITY_TITLE ?>" autocomplete=off>
                                </div>
                                <div class="form-group">
                                    <label for="description-<?php echo $data->ACTIVITY_ID ?>"  style="color: black; text-align: right;"><b>รายละเอียด</b></label>
                                    <textarea class="form-control" name="description-<?php echo $data->ACTIVITY_ID ?>" id="description-<?php echo $data->ACTIVITY_ID ?>" rows="4" cols="50"><?php echo $data->DESCRIPTION ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn btn-info" >วันที่เริ่มกิจกรรม&nbsp;&nbsp;</span>
                                        </div>
                                    </label>
                                    <label>
                                        <?php
                                        $StartDateCut = explode("/", $data->STARTDATE);
                                        $STimeDateCut  = explode(" ", $StartDateCut[2]);
                                        $STimeDateCutStep2  = explode(":", $STimeDateCut[1]);
                                        $STimeDateCutStep3 = $STimeDateCutStep2[0].":".$STimeDateCutStep2[1];
                                        $SYearDateCut = $STimeDateCut[0]-543;
                                        $SFullEndDateCut = $StartDateCut[0]."/".$StartDateCut[1]."/".$SYearDateCut." ".$STimeDateCutStep3;
                                        // Highlight today, show today button, change date format (use convertFormat
                                        // to auto convert PHP DateTime Format to DateTimePicker format).
                                        echo DateTimePicker::widget([
                                            'name' => 'startdate'."-".$data->ACTIVITY_ID,
                                            'id' => 'startdate'."-".$data->ACTIVITY_ID,
                                            'value' => $SFullEndDateCut,
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

                                        $EndDateCut = explode("/", $data->ENDDATE);
                                        $ETimeDateCut  = explode(" ", $EndDateCut[2]);
                                        $ETimeDateCutStep2  = explode(":", $ETimeDateCut[1]);
                                        $ETimeDateCutStep3 = $ETimeDateCutStep2[0].":".$ETimeDateCutStep2[1];
                                        $EYearDateCut = $ETimeDateCut[0]-543;
                                        $EFullEndDateCut = $EndDateCut[0]."/".$EndDateCut[1]."/".$EYearDateCut." ".$ETimeDateCutStep3;
                                        // Highlight today, show today button, change date format (use convertFormat
                                        // to auto convert PHP DateTime Format to DateTimePicker format).
                                        echo DateTimePicker::widget([
                                            'name' => 'enddate'."-".$data->ACTIVITY_ID,
                                            'id' => 'enddate'."-".$data->ACTIVITY_ID,
                                            'value' => $EFullEndDateCut,
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
                                    <select name="bgcolor-<?php echo $data->ACTIVITY_ID ?>" id="bgcolor-<?php echo $data->ACTIVITY_ID ?>">
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
                                    <label for="link-<?php echo $data->ACTIVITY_ID ?>"  style="color: black; text-align: right;"><b>Link</b></label>
                                    <input type="text" class="form-control" id="link-<?php echo $data->ACTIVITY_ID ?>" value="<?php echo $data->ACTIVITY_LINK ?>">
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer" id="Submit_Succes">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="Updata_event(
                                    document.getElementById('activity_title-<?php echo $data->ACTIVITY_ID ?>').value,
                                    document.getElementById('description-<?php echo $data->ACTIVITY_ID ?>').value,
                                    document.getElementById('startdate-<?php echo $data->ACTIVITY_ID ?>').value,
                                    document.getElementById('enddate-<?php echo $data->ACTIVITY_ID ?>').value,
                                    document.getElementById('bgcolor-<?php echo $data->ACTIVITY_ID ?>').value,
                                    document.getElementById('link-<?php echo $data->ACTIVITY_ID ?>').value,
                                    <?php echo $data->ACTIVITY_ID  ?>,
                                    '<?php echo $dateS."-".$dateE."-".$number_hn  ?>'
                                    )">ตกลง</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php }else{ ?>
            <td style="font-size:14px;color:black; background: #ff5b6a;""><b>Cancel</b></td>
            <td style="font-size:14px"></td>
            <td style="font-size:14px"></td>
        <?php } ?>

        <?php ++$number_hn; }}} ?>
    </tr>
    </tbody>
</table>
