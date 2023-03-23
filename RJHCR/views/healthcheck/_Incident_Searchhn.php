<?php
use yii\helpers\Html;


if (isset($API)&&isset($APILABGROUP)&&isset($dataLrfrlct)){ ?>
    <br>
    <table id="donationdata" style="padding: 20px; border: 2px solid rgba(0,0,0,0);">
        <thead>
        <tr style="border: 2px solid #000000; background: -webkit-linear-gradient(45deg, #343a40,#343a40); font-size:15px;">
            <th style="color:white" width="100 px"><b>HN</b></th>
            <th style="color:white" width="150 px"><b>วันที่รักษา</b></th>
            <th style="color:white" width="250 px"><b>ชื่อ - นามสกุล</b></th>
            <th style="color:white" width="150 px"><b>หน่วยงานที่รักษา</b></th>
            <th style="color:white" width="50 px">สถานะ <i class="fa fa-flask" style="font-size: 1.3em;" aria-hidden="true"></i></th>
            <th style="color:white" width="150 px">รายงานผลการตรวจสุขภาพ </th>
        </tr>
        </thead>

        <tbody>
        <?php

        if($API->json_result == true ){
        foreach ($API->json_data as $data){ //วน tr?>
        <tr onMouseover="this.style.backgroundColor='#ffc0c0';  this.style.color = 'black'; " onMouseout="this.style.backgroundColor='';  this.style.color = '';">
            <td  style="font-size:14px"><b style="color: black"><?php echo $data->HN; ?></b></td>
            <td style="font-size:14px"><b><?php echo $data->TDATE;?></b></td>
            <td style="font-size:14px"><b><?php echo $data->NAME;?></b></td>
            <td style="font-size:14px"><b><?php echo $data->CLINICLCTNAME;?></b></td>
            <td >
                <table>
                    <?php  foreach ($APILABGROUP->json_data as $datalab){ //วน tr ?>
                        <?php if($data->HN == $datalab->HN && $data->EDATE == $datalab->EDATE ){ ?>
                            <tr>
                                <?php if($datalab->STATUS == "Complete"){ ?>
                                    <td style="font-size:14px;color: #006e00"><b><?php echo $datalab->LABNAME;?></b></td>
                                    <td style="font-size:10px;color: #006e00" width="50 px"><i class="fas fa-file-alt" style="font-size: 2em;" aria-hidden="true"></i></td>
                                <?php }else{ ?>
                                    <td style="font-size:14px;color: red"><b><?php echo $datalab->LABNAME;?></b></td>
                                    <td style="font-size:10px;color: red" width="50 px"><i class="fas fa-file" style="font-size: 2em;" aria-hidden="true"></i></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>
            </td>
            <td style="font-size:14px">
                <hr style="color: rgba(255,0,0,0)">
                <?= Html::a('&nbsp;<i class="fa fa-print"  style="font-size: 1.3em;color: #e5e5e5" aria-hidden="true"></i>&nbsp;<b style="color: black">Print Report</b>&nbsp;','fpdfreport',
                    [
                        'class' => 'btn btn-danger btn-sm',
                        'target'=>'_blank',
                        'data'=>[
                            'method' => 'post',
                            'target'=>'_blank',
                            'params'=>[
                                'User[Lrfrlct]' => $dataLrfrlct,
                                'User[HN]' => $data->HN,
                                'User[NAME]' => $data->NAME,
                                'User[AGE]' => $data->AGE,
                                'User[SEX]' => $data->SEX,
                                'User[WEIGHT]' => $data->WEIGHT,
                                'User[HEIGHT]' => $data->HEIGHT,
                                'User[BT]' => $data->BT,
                                'User[RR]' => $data->RR,
                                'User[PR]' => $data->PR,
                                'User[H_LBP]' => $data->H_LBP,
                                'User[TDATE]' => $data->TDATE,
                                'User[EDATE]' => $data->EDATE,
                                'User[CLINICLCTNAME]' => $data->CLINICLCTNAME,
                            ],
                        ],
                    ]) ?>
                <hr style="color: rgba(255,0,0,0)">
            </td>
            <?php }
            } ?>
        </tr>
        </tbody>
    </table>
<?php } ?>



