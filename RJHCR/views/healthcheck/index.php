<?php

/* @var $this yii\web\View */
use common\widgets\Alert;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
Yii::$app->response->headers->add('Content-Type', 'pdfreport');

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


<!-- on your view layout file HEAD section -->
<script src="<?=\yii\helpers\Url::to('@web/../RJHCR/js/jquery.min.js'); ?>"></script>
<script src="<?=\yii\helpers\Url::to('@web/../RJHCR/js/all.js'); ?>"></script>

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
        background: #fcfdfc url(https://rjhome.rajavithi.go.th/assets/images/Loading/LoadindV7.gif) 50% 50% no-repeat ;
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

<div class="site-index main-contain">
    <br>
    <div class="row">
        <div class="form-group mb-12">
            <?php $form = ActiveForm::begin(); ?>
            <!----------------------------------------------------------------------------------------DateRangePicker----------------------------------------------------------------------------------------------------------------------->
            <div class="">
                <div class="row justify-content-md-center">
                    <div class="col col-lg-2">
                        <!-----------------------------------------------------------------------------------------------Loading---------------------------------------------------------------------------------------------------------------------------->
                        <div>
                            <span id="target"></span>
                        </div>
                        <div class="row" style="display:none">
                            <span id="replace_target"><img src="https://rjhome.rajavithi.go.th/RJESI/images/Loading.gif" style="width: 250px; height: 70px; margin: -19px;" ></span>
                        </div>
                    </div>

                    <div class="col">
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
                            'options' => ['placeholder' => 'Search Date...']
                        ]);
                        echo '</div>';
                        ?>
                    </div>
                    <div class="col col-lg-4">
                        <?php //lrfrlct
                            if (isset($Array_lrfrlct_api) && isset($dataLrfrlct)) {
                                //    ------------------------------------------------------------ Select2 ต้องใช้ แบบไม่ใช้ form ----------------------------------------------------------------
                                echo Select2::widget([
                                    'name' => 'LrfrlctType',
                                    'id' => 'LrfrlctType',
                                    'value' => $dataLrfrlct,
                                    'data' => $Array_lrfrlct_api,
                                    'size' => Select2::MEDIUM,
                                    'options' => [
                                        'placeholder' => 'Search หน่วยงานที่รักษา...',
    //                                    'required' => true ,
                                    ],
                                    'pluginOptions' => [
//                                        'allowClear' => true,
                                    ],
                                ]);
                            }
                        ?>
                    </div>
                    <div class="col col-lg-2">
                        <!-----------------------------------------------------------------------------------------------Button----------------------------------------------------------------------------------------------------------------------------->
                        <div class="container">
                            <?= Html::submitButton('Search', ['class' => 'btn btn-dark btn-block', 'name' => 'Check-button', 'value'=>1, 'onClick'=>"CheckDate('target', 'replace_target')"]) ?>
                        </div>
                    </div>

                </div>
            </div>
            <!--------------------------------------------------------------------------------------------------Alert------------------------------------------------------------------------------------------------------------------------------>
            <hr style="color: red; border: 8px solid red;border-radius: 10px;">
            <?= Alert::widget() ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="text-center bg-transparent">
        <h1 class="display-10">รายงานผลการตรวจสุขภาพ (Medical Check Up Report)</h1>
    </div>
    <!------------------------------------------------------------------------------------------------------ Search ----------------------------------------------------------------------------------------------------------------------------->
    <?php if (isset($API)&&isset($APILABGROUP)&&isset($dataLrfrlct)) { ?>
        <div class="card-body">
            <div class="input-group md-form form-sm form-1 pl-0" >
                <div class="input-group-prepend ">
                    <span class="input-group-text cyan lighten-2 " style="color: #ffffff; background-color:#343a40; " ><b>ค้นหา HN</b></span>
                </div>
                <input class="form-control my-0 py-1" type="text" id="Search_HN" placeholder="กรอก HN ที่จะค้นหา..." aria-label="Search"  autocomplete="off" onkeypress ="return SearchHN(this.value)">
                <button class="btn btn-secondary" data-toggle="tooltip"  title="ค้นหา HN ไม่สนใจวันที่ คลิก!!" type="button" onclick="SearchHNALL(Search_HN.value)">
                    <b>Search HN</b>
                </button>
                <button class="btn btn-secondary" data-toggle="tooltip"  title="ค้นหา HN ไม่สนใจวันที่ คลิก!!" type="button" onclick="SearchHNALL(Search_HN.value)">
                    <i class="fas fa-search text-dark" aria-hidden="true" ></i>
                </button>
            </div>
            <div id="Box_Incident_Searchhn" >
                <?php
                    echo $this->render('_Incident_Searchhn', [
                        'APILABGROUP' => $APILABGROUP,
                        'dataLrfrlct' => $dataLrfrlct,
                        'API' => $API,
                    ]) ;
                ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php
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

    //======================= เมาส์ชี้ แล้วขึ้นข้อความ HTML สวยๆ ด้วย Bootstrap Tooltip =================================
    $(function () {
        //========================================รอโหลดหน้า===============================================
        $("#overlay").fadeOut();
        $(".main-contain").removeClass("main-contain");
        //===============================================================================================
        $('[data-toggle="tooltip"]').tooltip();
    });
    //เรียก script <script src="https://code.jquery.com/jquery-3.5.1.min.js">
    //นำข้อความนี้ไปใส่ data-toggle="tooltip"  title="คลิกเพื่ออ่านรายละเอียดต่อ"
    //=================================================================================================

    function CheckDate(target, source) {
        //========================================ซ่อนและแสดงรูป===============================================
        document.getElementById(target).innerHTML = document.getElementById(source).innerHTML;
        //===============================================================================================
    }

    function SearchHN( data ) {
        <?php if (isset($dataLrfrlct)){ ?>
            <?php if (isset($SDate)&&isset($EDate)) {?>
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['healthcheck/searchhn']) ?>',
                type: 'POST',
                data: 'HN='+data+'&SDate=<?=$SDate?>'+'&EDate=<?=$EDate?>'+'&dataLrfrlct=<?=$dataLrfrlct?>',
                success: function (data) {
                    $('#Box_Incident_Searchhn').html(data);
                }
            });
            <?php }else{  $ToDay = date('dmY');?>
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['healthcheck/searchhn']) ?>',
                type: 'POST',
                data: 'HN='+data+'&SDate=<?=$ToDay?>'+'&EDate=<?=$ToDay?>'+'&dataLrfrlct=<?=$dataLrfrlct?>',
                success: function (data) {
                    $('#Box_Incident_Searchhn').html(data);
                }
            });
            <?php } ?>
        <?php } ?>

        //ไม่ให้พิมตัวอักษร ====================================================================================
        var vchar = String.fromCharCode(event.keyCode);
        if ((vchar < '0' || vchar > '9') && (vchar !== '.')) return false;
        e.onKeyPress = vchar;
        //===============================================================================================
    }

    function SearchHNALL( data ) {
        <?php if (isset($dataLrfrlct)){ ?>
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['healthcheck/searchhnall']) ?>',
                type: 'POST',
                data: 'HN='+data+'&dataLrfrlct=<?=$dataLrfrlct?>',
                success: function (data) {
                    $('#Box_Incident_Searchhn').html(data);
                }
            });
        <?php } ?>
    }

    function Open_Report ( data ) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['healthcheck/fpdfreport']) ?>',
            type: 'POST',
            data: 'HN='+data,
            success: function (data) {

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