
<?php if (isset($Data_date_event )) { ?>

    <?php if($Data_date_event == []){ ?>
        <img src="https://rjhome.rajavithi.go.th/RJActivityTime/images/TimeLineV3.png" alt="login"  style="width: 98% ; height: 98%">
    <?php }else{ ?>
        <div class="card text-white mb-3" id="Box_Data_Eventgroup" style="background-color: #f3969a;color:black;width: 100%;height: 100%">
            <div class="card-header text-center" style="color:black;"><b>รายละเอียดกิจกรรม</b></div>
            <div class="card-body" style="overflow: auto;height: 100px">
                <?php foreach ($Data_date_event->json_data as $Data_event){
                    if($Data_event->CANCELDATE == null){?>
                    <div class="card text-dark mb-3" style="color:black;width: 100%; font-size: 14px">
                        <div class="card-body">
                            <div class="card-body">
                                <b style="color:#ff5b6a;">ชื่อกิจกรรม :</b> <b style="color:black;"><?php echo $Data_event->ACTIVITY_TITLE ?></b> <p ></p>
                                <b style="color:#ff5b6a;">รายละเอียด :</b> <?php echo nl2br($Data_event->DESCRIPTION) ?><p ></p>
                                <b style="color:#ff5b6a;">วันที่ :</b> <b style="color:black;"> <?php echo $Data_event->STARTDATE." ถึงวัน ".$Data_event->ENDDATE ?> </b>
                                <?php if($Data_event->ACTIVITY_LINK != null){ ?>
                                    <a href="<?php echo $Data_event->ACTIVITY_LINK ?>" target="_blank" class="btn btn-dark btn-sm float-right" style="text-align:right;">&nbsp;เว็บที่เกียวข้อง&nbsp;</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php }} ?>
            </div>
        </div>
    <?php }?>

<?php } ?>

