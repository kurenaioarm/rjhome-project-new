<?php
namespace  RJHCR\controllers;

use function Psr\Log\alert;
use \yii\web\Controller;
use RJHCR\models\StatuserForm;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use yii\helpers\Html;
use Yii;

class HealthcheckController extends Controller
{

    public function actionIndex()
    {
        $StatuserModel = new StatuserForm();
        $API = null;
        $dateSS = null;
        $dateEE = null;
        $ToDay = date('dmY');

        //============== API RJCHR Check Token==============
        $dataLrfrlct = '302090300';
        $API = $this->API_checkup_queue($ToDay, $ToDay,$dataLrfrlct," ");
        $APILABGROUP =  $this->API_checkup_labgroup($ToDay, $ToDay,$dataLrfrlct);
        //=================================================

        if($API->json_result == true){
            $API_checkup_lrfrlct = $this->API_checkup_lrfrlct();
            //============================================== สร้าง Array หน่วยงานที่รักษาทั้งหมด =======================================================
            $Array_lrfrlct_api = array();
            if (isset($Array_lrfrlct_api)) {
                foreach ($API_checkup_lrfrlct->json_data as $data_checkup_lrfrlct){
                    $DataID_Lrfrlct =  $data_checkup_lrfrlct->LRFRLCT;
                    $DataNAME_Lrfrlct =  $data_checkup_lrfrlct->DSPNAME ;
                    $Array_lrfrlct_api[$DataID_Lrfrlct]=$DataNAME_Lrfrlct; //การสร้าง Array แบบกำหนด  key value
                }
            }

            if(Yii::$app->request->post()) {
                $StatuserModel->load(Yii::$app->request->post());
                $SelectedDate = $StatuserModel->SelectedDate;
                $SelectedLrfrlct = Yii::$app->request->post("LrfrlctType");

                $CheckValue = explode( "-",Yii::$app->request->post('Check-button'));
                if($CheckValue[0] == 2){

                }else{
                    if($SelectedDate == "" ){ // Check วันที่ ส่งมา
                        Yii::$app->session->setFlash('error', 'กรุณาระบุวันที่');
                    }else {
                        if($SelectedLrfrlct == "" ){ // Check หน่วยงานที่รักษาที่ ส่งมา
                            Yii::$app->session->setFlash('error', 'กรุณาระบุหน่วยงานที่รักษา');
                        }else {
                            $dataLrfrlct = $SelectedLrfrlct;
                            $SelectedDateCut = explode(" - ", $SelectedDate);
                            $dateS = $SelectedDateCut[0];
                            $dateE = $SelectedDateCut[1];

                            // แปลงวันวันที่ / =====================================================
                            $datecutS = explode( "/",$dateS);
                            $datecutE = explode( "/",$dateE);
                            // แปลงวันวันที่  =====================================================
                            $dateSS = $datecutS[0].$datecutS[1].$datecutS[2];
                            $dateEE = $datecutE[0].$datecutE[1].$datecutE[2];
                            // แปลงวันวันที่ - =====================================================
                            $dateSV2 = $datecutS[2]."-".$datecutS[1]."-".$datecutS[0];
                            $dateEV2 = $datecutE[2]."-".$datecutE[1]."-".$datecutE[0];
                            // แปลงวันที่่ไทย ======================================================
                            $SDateThai = Yii::$app->helper->dateThaiFull($dateSV2); // แปลงวันที่่ไทย
                            $EDateThai = Yii::$app->helper->dateThaiFull($dateEV2); // แปลงวันที่่ไทย
                            //===============================================================

                            //================= หาจำนวนวันที่ ไม่เกิน 30 วัน กรณีคนละเดือน คนละปี ===============
                            $SDayEnd = date("Y-m-t", strtotime($dateSV2)); //หาวันสุดท้ายของเดือนที่เลือก
                            $SDayEndCut = explode( "-",$SDayEnd);
                            if($datecutS[2] == $datecutE[2] && $datecutS[1] == $datecutE[1]){
                                $SUMDayCheck = "0";
                            }else{
                                $SUMDayCheck = ($SDayEndCut[2] - $datecutS[0])+ 1 + $datecutE[0];
                            }
                            //==============================================================
                            if(Yii::$app->request->post('Check-button') == 1){
                                if($datecutS[2] == $datecutE[2] && $datecutS[1] == $datecutE[1] ||  $SUMDayCheck <= 30){ // Check ว่าเป็น เดือนเดียว ปีเดียว กันไหม
                                    //========================== API RJHCR =========================
                                    $API = $this->API_checkup_queue($dateSS, $dateEE,$dataLrfrlct,"");
                                    $APILABGROUP =  $this->API_checkup_labgroup($dateSS, $dateEE,$dataLrfrlct);
                                    //===========================================================
                                }else{
                                    $API = null;
                                    Yii::$app->session->setFlash('error', 'ไม่สามารถตรวจสอบข้อมูลได้ <b><u>วันที่เลือกย้อนหลังต้องไม่เกิน 30 วัน</u></b> กรุณาระบุวันที่ใหม่อีกครั้ง');
                                }
                            }else{

                            }
                        }
                    }
                }
            }
        }else{
            Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
            return $this->redirect(['login/login_his']);
        }

        return $this->render('index', [
            'model' => $StatuserModel,
            'SDate' => $dateSS,
            'EDate' => $dateEE,
            'APILABGROUP' => $APILABGROUP,
            'Array_lrfrlct_api' => $Array_lrfrlct_api,
            'dataLrfrlct' => $dataLrfrlct,
            'API' => $API
        ]);
    }

    public function actionSearchhn()//Search
    {

        //========================== API ESI =========================
        $API = $this->API_checkup_queue(Yii::$app->request->post("SDate"), Yii::$app->request->post("EDate"),Yii::$app->request->post("dataLrfrlct"),Yii::$app->request->post("HN"));
        $APILABGROUP =  $this->API_checkup_labgroup(Yii::$app->request->post("SDate"), Yii::$app->request->post("EDate"),Yii::$app->request->post("dataLrfrlct"));
        //===========================================================
        return $this->renderAjax('_Incident_Searchhn', [
            'APILABGROUP' => $APILABGROUP,
            'dataLrfrlct' => Yii::$app->request->post("dataLrfrlct"),
            'API' => $API
        ]);
    }

    public function actionSearchhnall()//Search
    {
        //========================== API ESI =========================
        $API = $this->API_checkup_hnall(Yii::$app->request->post("HN"),Yii::$app->request->post("dataLrfrlct"));
        $APILABGROUP =  $this->API_checkup_hnalllabgroup(Yii::$app->request->post("HN"),Yii::$app->request->post("dataLrfrlct"));
        //===========================================================
        return $this->renderAjax('_Incident_Searchhn', [
            'APILABGROUP' => $APILABGROUP,
            'dataLrfrlct' => Yii::$app->request->post("dataLrfrlct"),
            'API' => $API
        ]);
    }

    public function actionError()
    {
        return $this->render('error');
    }

    public function actionFpdfreport()
    {

        if(Yii::$app->request->post("User") == null){
            return $this->redirect(['error']);
        }else{
            $Access_Staff = Yii::$app->session->get('arrayAccess_Staff');
            date_default_timezone_set('Asia/Bangkok');
            require(dirname(__FILE__).'/../../web/assets/vendor_add_on/FPDF/fpdf.php');

            //==================== วัน/เดือน/ปี ปัจจุบัน ========================
            $ToDay = date('d/m/Y');

            $ToDayCut = explode( "/",$ToDay);
            $YearTH = $ToDayCut[2]+543;
            $ToDayTH = $ToDayCut[0]."/".$ToDayCut[1]."/".$YearTH;
            //=========================================================
            $this->API_stack_download($ToDay,$Access_Staff,Yii::$app->request->post("User")["Lrfrlct"]);
            //====================== ค่าตัวแปล User ========================
            $User = Yii::$app->request->post("User");
            $HN_Set1 = substr($User["HN"], 2);
            $HN_Set2 = substr($User["HN"], 0,2);
            $HN_Set3 = $HN_Set1." - ".$HN_Set2;
            //=========================================================

            //================ คำนวณ ดัชนีมวลกาย (BMI) ======================
            if($User["WEIGHT"] == null && $User["HEIGHT"] == null){
                $BMI = "";
            }else{
                $BMI =  round($User["WEIGHT"] / ( ( $User["HEIGHT"] / 100 ) ** 2 ));
            }
            //=========================================================

            //=========================== API ESI =========================
            $TDATE = explode( " ",$User["TDATE"]);
            if($TDATE[1] == "ม.ค."){
                $TMONTH = "01";
            }else if($TDATE[1] == "ก.พ."){
                $TMONTH = "02";
            }else if($TDATE[1] == "มี.ค."){
                $TMONTH = "03";
            }else if($TDATE[1] == "เม.ย."){
                $TMONTH = "04";
            }else if($TDATE[1] == "พ.ค."){
                $TMONTH = "05";
            }else if($TDATE[1] == "มิ.ย."){
                $TMONTH = "06";
            }else if($TDATE[1] == "ก.ค."){
                $TMONTH = "07";
            }else if($TDATE[1] == "ส.ค."){
                $TMONTH = "08";
            }else if($TDATE[1] == "ก.ย."){
                $TMONTH = "09";
            }else if($TDATE[1] == "ต.ค."){
                $TMONTH = "10";
            }else if($TDATE[1] == "พ.ย."){
                $TMONTH = "11";
            }else if($TDATE[1] == "ธ.ค."){
                $TMONTH = "12";
            }
            if (isset($TMONTH)) {
                $TDATETH =  $TDATE[0]."/".$TMONTH."/".$TDATE[2];
            }
            $APILAB = $this->API_checkup_lab($User["EDATE"],$User["HN"],Yii::$app->request->post("User")["Lrfrlct"]);
            //===========================================================

            if($APILAB->json_result == true){
                //========================================================== ตั่งค่าหน้ากระดาษ =====================================================================
                $pdf=new \FPDF( 'P' , 'mm' , 'A4' ); //(orientation, 'mm' , format)  สร้าง instant FPDF
                //^==================== orientation ==========================
                //P – แนวตั้ง (default)     //L – แนวนอน
                //^====================== format ============================
                //A3        //A4 (default)      //A5        //Letter        //array(width,height) – กำหนดเอง โดยส่งอะเรย์ กว้างxสูง
                $pdf->SetAuthor( 'RajavithiHospital' ); //กำหนดชื่อเจ้าของเอกสาร
                $pdf->SetCreator( 'fpdf version 1.84' ); //สำหรับกำหนดชื่อผู้สร้างเอกสาร โดยทั่วไปแล้วจะใช้เป็นชื่อแอพพลิเคชั่นที่สร้างไฟล์ pdf
                $pdf->SetDisplayMode( 'fullpage' , 'single' ); //(zoom ,layout)ไฟล์ pdf นั้นเวลาเปิด ให้เลือกว่าจะดูแบบ เต็มหน้ากระดาษ หรือเต็มความกว้างของหน้าจอ และอีกหลายตัวเลือก คำสั่งนี้อนุญาติให้เรากำหนดโหมดที่จะให้ user เห็นตั้งแต่เปิดเอกสาร
                //^====================== zoom ============================
                //fullpage – แสดงเอกสารเต็มหน้าหน้าดาษ เห็นทั้งหน้าเต็มๆ
                //fullwidth – แสดงเอกสารเต็มหน้าด้านกว้าง
                //real – แสดงเอกสาร 100%
                //default – ปล่อยไปตามที่ยูสเซ่อร์กำหนดไว้ในโปรแกรม adobe reader
                //^====================== layout ============================
                //single – แสดงครั้งละ 1 หน้าเต็ม
                //continuous – (default) แสดงหน้าแบบต่อเนื่อง
                //two – แสดงครั้งละ 2 หน้า
                //default – ปล่อยไปตามที่ยูสเซ่อร์กำหนดไว้ในโปรแกรม adobe
                $pdf->AddFont('THSarabunNew','','THSarabunNew.php'); //เพิ่ม Font ธรรมดา
                $pdf->AddFont('THSarabunNew','B','THSarabunNew Bold.php'); //เพิ่ม Font หนา
                $pdf->AddFont('THSarabunNew','I','THSarabunNew Italic.php'); //เพิ่ม Font เอียง
                $pdf->AddFont('THSarabunNew','BI','THSarabunNew BoldItalic.php'); //เพิ่ม Font หนาเอี
                $pdf->SetSubject( 'this document for RJHCR.' ); //สำหรับกำหนด subject ของเอกสาร
                $pdf->SetTitle( 'Medical Check Up Report' ); //สำหรับกำหนด title ของเอกสาร
                $pdf->SetAutoPageBreak(false);// set a bottom margin in FPDF
                $pdf->AddPage();//เพิ่มหน้ากระดาษ
                //==========================================================================================================================================

                //==================================================== ตัวอย่าง =====================================================
                //        $pdf->SetFont('THSarabunNew','B',16); //กำหนดฟอนต์ Arial ตัวหนา ขนาด 16
                //        $pdf->Text( 10 , 14 , 'Hello World!'); //พิมพ์คำว่า Hello World! ลงไปในตำแหน่ง  //เยื้องจากขอบกระดาษด้านซ้าย 10 มม. //เยื้องจากขอบกระดาษด้านบน 10 มม.
                //===============================================================================================================

                //==================================================== วัดค่ากระดาษ =====================================================
                //        $pdf->SetFont('THSarabunNew','B',16);
                //        $pdf->Text( 11 , 3 , iconv( 'UTF-8','cp874' , '|1' ));
                //        $pdf->Text( 21 , 3 , iconv( 'UTF-8','cp874' , '|2' ));
                //        $pdf->Text( 31 , 3 , iconv( 'UTF-8','cp874' , '|3' ));
                //        $pdf->Text( 41 , 3 , iconv( 'UTF-8','cp874' , '|4' ));
                //        $pdf->Text( 51 , 3 , iconv( 'UTF-8','cp874' , '|5' ));
                //        $pdf->Text( 61 , 3 , iconv( 'UTF-8','cp874' , '|6' ));
                //        $pdf->Text( 71 , 3 , iconv( 'UTF-8','cp874' , '|7' ));
                //        $pdf->Text( 81 , 3 , iconv( 'UTF-8','cp874' , '|8' ));
                //        $pdf->Text( 91 , 3 , iconv( 'UTF-8','cp874' , '|9' ));
                //        $pdf->Text( 101 , 3 , iconv( 'UTF-8','cp874' , '|10' ));
                //        $pdf->Text( 111 , 3 , iconv( 'UTF-8','cp874' , '|11' ));
                //        $pdf->Text( 121 , 3 , iconv( 'UTF-8','cp874' , '|12' ));
                //        $pdf->Text( 131 , 3 , iconv( 'UTF-8','cp874' , '|13' ));
                //        $pdf->Text( 141 , 3 , iconv( 'UTF-8','cp874' , '|14' ));
                //        $pdf->Text( 151 , 3 , iconv( 'UTF-8','cp874' , '|15' ));
                //        $pdf->Text( 161 , 3 , iconv( 'UTF-8','cp874' , '|16' ));
                //        $pdf->Text( 171 , 3 , iconv( 'UTF-8','cp874' , '|17' ));
                //        $pdf->Text( 181 , 3 , iconv( 'UTF-8','cp874' , '|18' ));
                //        $pdf->Text( 191 , 3 , iconv( 'UTF-8','cp874' , '|19' ));
                //        $pdf->Text( 201 , 3 , iconv( 'UTF-8','cp874' , '|20' ));
                //
                //        $pdf->Text( 0 , 11 , iconv( 'UTF-8','cp874' , '__1' ));
                //        $pdf->Text( 0 , 21 , iconv( 'UTF-8','cp874' , '__2' ));
                //        $pdf->Text( 0 , 31 , iconv( 'UTF-8','cp874' , '__3' ));
                //        $pdf->Text( 0 , 41 , iconv( 'UTF-8','cp874' , '__4' ));
                //        $pdf->Text( 0 , 51 , iconv( 'UTF-8','cp874' , '__5' ));
                //        $pdf->Text( 0 , 61 , iconv( 'UTF-8','cp874' , '__6' ));
                //        $pdf->Text( 0 , 71 , iconv( 'UTF-8','cp874' , '__7' ));
                //        $pdf->Text( 0 , 81 , iconv( 'UTF-8','cp874' , '__8' ));
                //        $pdf->Text( 0 , 91 , iconv( 'UTF-8','cp874' , '__9' ));
                //        $pdf->Text( 0 , 101 , iconv( 'UTF-8','cp874' , '__10' ));
                //        $pdf->Text( 0 , 111 , iconv( 'UTF-8','cp874' , '__11' ));
                //        $pdf->Text( 0 , 121 , iconv( 'UTF-8','cp874' , '__12' ));
                //        $pdf->Text( 0 , 131 , iconv( 'UTF-8','cp874' , '__13' ));
                //        $pdf->Text( 0 , 141 , iconv( 'UTF-8','cp874' , '__14' ));
                //        $pdf->Text( 0 , 151 , iconv( 'UTF-8','cp874' , '__15' ));
                //        $pdf->Text( 0 , 161 , iconv( 'UTF-8','cp874' , '__16' ));
                //        $pdf->Text( 0 , 171 , iconv( 'UTF-8','cp874' , '__17' ));
                //        $pdf->Text( 0 , 181 , iconv( 'UTF-8','cp874' , '__18' ));
                //        $pdf->Text( 0 , 191 , iconv( 'UTF-8','cp874' , '__19' ));
                //        $pdf->Text( 0 , 201 , iconv( 'UTF-8','cp874' , '__20' ));
                //        $pdf->Text( 0 , 211 , iconv( 'UTF-8','cp874' , '__21' ));
                //        $pdf->Text( 0 , 221 , iconv( 'UTF-8','cp874' , '__22' ));
                //        $pdf->Text( 0 , 231 , iconv( 'UTF-8','cp874' , '__23' ));
                //        $pdf->Text( 0 , 241 , iconv( 'UTF-8','cp874' , '__24' ));
                //        $pdf->Text( 0 , 251 , iconv( 'UTF-8','cp874' , '__25' ));
                //        $pdf->Text( 0 , 261 , iconv( 'UTF-8','cp874' , '__26' ));
                //        $pdf->Text( 0 , 271 , iconv( 'UTF-8','cp874' , '__27' ));
                //        $pdf->Text( 0 , 281 , iconv( 'UTF-8','cp874' , '__28' ));
                //        $pdf->Text( 0 , 291 , iconv( 'UTF-8','cp874' , '__29' ));
                //===============================================================================================================


                //=================================================== Medical Check Up Report ================================================================
                $pdf->Image('../../web/assets/images/logo/LogoRJ_V1.png',11,10,17,0);
                $pdf->SetFont('THSarabunNew','B',17);
                $pdf->Text( 32, 15 , iconv( 'UTF-8','cp874' , 'โรงพยาบาลราชวิถี' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 32, 23 , iconv( 'UTF-8','cp874' , 'ห้องตรวจสุขภาพ โทร. 02-2069000 ต่อ 10205' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 155, 23 , iconv( 'UTF-8','cp874' , 'ลำดับที่ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 169 , 23 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _' ));



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 11, 34  , iconv( 'UTF-8','cp874' , 'ชื่อ - นามสกุล :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 33 , 34 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 39, 34  , iconv( 'UTF-8','cp874' , $User["NAME"] ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 108, 34  , iconv( 'UTF-8','cp874' , 'หน่วยงาน :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 124 , 34 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 130, 34  , iconv( 'UTF-8','cp874' , $User["CLINICLCTNAME"] ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 11, 42  , iconv( 'UTF-8','cp874' , 'HN :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 18 , 42 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 23, 42  , iconv( 'UTF-8','cp874' ,$HN_Set3));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 50, 42  , iconv( 'UTF-8','cp874' , 'อายุ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 58 , 42 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 65, 42  , iconv( 'UTF-8','cp874' ,$User["AGE"]));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 88, 42  , iconv( 'UTF-8','cp874' , 'ปี' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 94, 42  , iconv( 'UTF-8','cp874' , 'เพศ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 102 , 42 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 111, 42  , iconv( 'UTF-8','cp874' ,$User["SEX"]));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 133, 42  , iconv( 'UTF-8','cp874' , 'วันที่ตรวจเลือด :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 156 , 42 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 167, 42  , iconv( 'UTF-8','cp874' ,$ToDayTH));



                $pdf->SetFont('THSarabunNew','B',17);
                $pdf->Text( 53, 51  , iconv( 'UTF-8','cp874' , 'รายงานผลการตรวจสุขภาพ (Medical Check Up Report)' ));



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 11, 60  , iconv( 'UTF-8','cp874' , 'ผลการตรวจร่างกาย' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 42, 60  , iconv( 'UTF-8','cp874' , 'รอบเอว :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 55, 60 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 69, 60  , iconv( 'UTF-8','cp874' , 'ซม.' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 77, 60  , iconv( 'UTF-8','cp874' , 'น้ำหนัก :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 90, 60 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 93, 60  , iconv( 'UTF-8','cp874' ,$User["WEIGHT"]));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 104, 60  , iconv( 'UTF-8','cp874' , 'กก.' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 113, 60  , iconv( 'UTF-8','cp874' , 'ส่วนสูง :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 125, 60 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 128, 60  , iconv( 'UTF-8','cp874' ,$User["HEIGHT"]));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 139, 60  , iconv( 'UTF-8','cp874' , 'ซม.' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 148, 60  , iconv( 'UTF-8','cp874' , 'ดัชนีมวลการ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 168, 60 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 178, 60  , iconv( 'UTF-8','cp874' ,$BMI));



                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 68  , iconv( 'UTF-8','cp874' , 'อุณหภูมิ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 25 , 68 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 29, 68  , iconv( 'UTF-8','cp874' ,$User["BT"]));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 41, 68  , iconv( 'UTF-8','cp874' , 'องศา' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 51, 68  , iconv( 'UTF-8','cp874' , 'หายใจ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 63 , 68 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 68, 68  , iconv( 'UTF-8','cp874' ,$User["RR"]));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 79, 68  , iconv( 'UTF-8','cp874' , 'ครั้ง/นาที' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 95, 68  , iconv( 'UTF-8','cp874' , 'ชีพจร :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 106 , 68 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 111, 68  , iconv( 'UTF-8','cp874' ,$User["PR"]));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 122, 68  , iconv( 'UTF-8','cp874' , 'ครั้ง/นาที' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 138, 68  , iconv( 'UTF-8','cp874' , 'ความดันโลหิต :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 159, 68 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 165, 68  , iconv( 'UTF-8','cp874' ,$User["H_LBP"]));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 185, 68  , iconv( 'UTF-8','cp874' , 'มม.ปรอท' ));



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 11, 79  , iconv( 'UTF-8','cp874' , 'ความสมบูรณ์ของเม็ดเลือด (Complete Blood Count)' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 90, 79  , iconv( 'UTF-8','cp874' , 'CBC' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 103, 79  , iconv( 'UTF-8','cp874' , 'ปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 115, 79  , iconv( 'UTF-8','cp874' , 'ผิดปกติ' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 86  , iconv( 'UTF-8','cp874' , 'ความเข้มข้นของเลือด Hb :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 47, 86 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "3075"){//ความเข้มข้นของเลือด HBG
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 58, 86  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 58, 86  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 86  , iconv( 'UTF-8','cp874' , 'g/dL (ชาย:14.0-17.4 หญิง:12.3-15.3)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 93  , iconv( 'UTF-8','cp874' , 'ความเข้มข้นของเลือด Hct :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 48, 93 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "3076"){//ความเข้มข้นของเลือด Hct
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 58, 93  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 58, 93  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 93  , iconv( 'UTF-8','cp874' , '% (ชาย:41.5-50.4 หญิง:36-45)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 100  , iconv( 'UTF-8','cp874' , 'เม็ดเลือดขาว :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 31, 100 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "3073"){//เม็ดเลือดขาว WBC
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 50, 100  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 50, 100  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 100  , iconv( 'UTF-8','cp874' , '10^3/uL (4.4-11.3)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 107  , iconv( 'UTF-8','cp874' , 'จำนวนเกล็ดเลือด :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 37, 107 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "3080"){//จำนวนเกล็ดเลือด PLT
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 50, 107  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 50, 107  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 107  , iconv( 'UTF-8','cp874' , '10^3/uL (150-450)' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 11, 114  , iconv( 'UTF-8','cp874' , 'ลักษณะเม็ดเลือด :' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 40, 114  , iconv( 'UTF-8','cp874' , "-" ));
//            foreach($APILAB->json_data as $datalab){
//                if($datalab->LABEXM == "3108"){//จำนวนเกล็ดเลือด PLT
//                }
//            }
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 11, 124  , iconv( 'UTF-8','cp874' , 'Approved by :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 34, 124 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "3075"){//ความเข้มข้นของเลือด HBG DSPNAME
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->DSPNAME == null){
                            $pdf->Text( 40, 124  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 40, 124  , iconv( 'UTF-8','cp874' , $datalab->DSPNAME ));
                        }
                    }
                }



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 130, 79  , iconv( 'UTF-8','cp874' , 'การตรวจปัสสาวะ (Urine examination)' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 135, 86  , iconv( 'UTF-8','cp874' , 'ปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 149, 86  , iconv( 'UTF-8','cp874' , 'ผิดปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 167, 86  , iconv( 'UTF-8','cp874' , 'ไม่ส่งตรวจ' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 130, 93  , iconv( 'UTF-8','cp874' , 'โปรตีน :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 142, 93 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "5047"){//โปรตีน Protein
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 145, 93  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 145, 93  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 165, 93  , iconv( 'UTF-8','cp874' , 'น้ำตาล :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 177, 93 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "5048"){//น้ำตาล Glucose
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 180, 93  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 180, 93  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 130, 100  , iconv( 'UTF-8','cp874' , 'เม็ดเลือดแดง :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 150, 100 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "5039"){//เม็ดเลือดแดง Red blood cell
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 165, 100  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 165, 100  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 187, 100  , iconv( 'UTF-8','cp874' , 'cell/HPF' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 130, 107  , iconv( 'UTF-8','cp874' , 'เม็ดเลือดขาว :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 150, 107 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "5040"){//เม็ดเลือดขาว White blood cell
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 165, 107  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 165, 107  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 187, 107  , iconv( 'UTF-8','cp874' , 'cell/HPF' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 130, 114  , iconv( 'UTF-8','cp874' , 'Approved by :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 153, 114 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "5047"){//โปรตีน Protein DSPNAME
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->DSPNAME == null){
                            $pdf->Text( 160, 114  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 160, 114  , iconv( 'UTF-8','cp874' , $datalab->DSPNAME ));
                        }
                    }
                }



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 11, 134  , iconv( 'UTF-8','cp874' , 'สารเคมีในเลือด (Blood Chemistry)' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 103, 134  , iconv( 'UTF-8','cp874' , 'ปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 115, 134  , iconv( 'UTF-8','cp874' , 'ผิดปกติ' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 141  , iconv( 'UTF-8','cp874' , 'ระดับน้ำตาลในเลือด :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 41, 141 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1069"){//ระดับน้ำตาลในเลือด Glucose
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 57, 141  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 57, 141  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 141  , iconv( 'UTF-8','cp874' , 'mg/dl (74-100)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 148  , iconv( 'UTF-8','cp874' , 'การทำงานของไต (BUN) :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 46, 148 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1070"){//การทำงานของไต (BUN)
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 57, 148  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 57, 148  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',12);
                $pdf->Text( 76, 146  , iconv( 'UTF-8','cp874' , 'mg/dl (<50;ช:8.9-20.6;ญ:7.0-18.7)' ));
                $pdf->Text( 76, 150  , iconv( 'UTF-8','cp874' , 'mg/dl (>50;ช:8.4-25.7;ญ:9.8-20.1)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 155  , iconv( 'UTF-8','cp874' , 'การทำงานของไต (Cr) :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 43, 155 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1071"){//การทำงานของไต (Cr) Creatinine
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 57, 155  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 57, 155  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 155  , iconv( 'UTF-8','cp874' , 'mg/dl (ช:0.73-1.18 ญ:0.55-1.02)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 162  , iconv( 'UTF-8','cp874' , 'ประสิทธิภาพการกรองของไต (eGFR) :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 62, 162 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1160"){//ประสิทธิภาพการกรองของไต (eGFR)
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 65, 162  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 65, 162  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 162  , iconv( 'UTF-8','cp874' , 'mL/min/1.73m^2 (>=90)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 169  , iconv( 'UTF-8','cp874' , 'กรดยูริค :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 25, 169 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1072"){//กรดยูริค Uric acid
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 48, 169  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 48, 169  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 169  , iconv( 'UTF-8','cp874' , 'mg/dl (ช:3.5-7.2 ญ:2.6-6.0)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 176  , iconv( 'UTF-8','cp874' , 'ไขมันในเลือดโคเลสเตอรอล :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 49, 176 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1083"){//ไขมันในเลือดโคเลสเตอรอล Cholesterol
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 59, 176  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 59, 176  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 176  , iconv( 'UTF-8','cp874' , 'mg/dl (<200 )'));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 183  , iconv( 'UTF-8','cp874' , 'ไขมันในเลือดไตรกลีเซอไรด์ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 49, 183 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1084"){//ไขมันในเลือดไตรกลีเซอไรด์ Triglyceride
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 59, 183  , iconv( 'UTF-8','cp874' ,"" ));
                        }else{
                            $pdf->Text( 59, 183  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 183  , iconv( 'UTF-8','cp874' , 'mg/dl (<150 )'));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 190  , iconv( 'UTF-8','cp874' , 'การทำงานของตับ (SGOT) :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 48, 190 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1080"){//การทำงานของตับ (SGOT)
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 59, 190  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 59, 190  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 190  , iconv( 'UTF-8','cp874' , 'U/L (5-34)'));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 197  , iconv( 'UTF-8','cp874' , 'การทำงานของตับ (SGPT) :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 47, 197 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1081"){//การทำงานของตับ (SGPT)
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 59, 197  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 59, 197  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 197  , iconv( 'UTF-8','cp874' , 'U/L (0-55)'));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 204  , iconv( 'UTF-8','cp874' , 'การทำงานของตับ (ALP) :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 45, 204 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1082"){//การทำงานของตับ (ALP) Alkaline phosphatase
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 59, 204  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 59, 204  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 204  , iconv( 'UTF-8','cp874' , 'U/L (40-150)'));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 211  , iconv( 'UTF-8','cp874' , 'ความหนาแน่นของไขมันดี (HDL) :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 56, 211 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1085"){//ความหนาแน่นของไขมันดี (HDL) HDL Cholesterol
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 64, 211  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 64, 211  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 211  , iconv( 'UTF-8','cp874' , 'U/L (40-150)'));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 218  , iconv( 'UTF-8','cp874' , 'ความหนาแน่นของไขมันเลว (LDL) :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 58, 218 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1086"){//ความหนาแน่นของไขมันเลว (LDL) LDL Cholesterol
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 64, 218  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 64, 218  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 218  , iconv( 'UTF-8','cp874' , 'U/L (40-150)'));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 11, 228  , iconv( 'UTF-8','cp874' , 'Approved by :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 34, 228 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "1069"){//ระดับน้ำตาลในเลือด Glucose DSPNAME
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->DSPNAME == null){
                            $pdf->Text( 40, 228  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 40, 228  , iconv( 'UTF-8','cp874' , $datalab->DSPNAME ));
                        }
                    }
                }



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 130, 127  , iconv( 'UTF-8','cp874' , 'การตรวจอุจจาระ (Stool examination)' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 135, 134  , iconv( 'UTF-8','cp874' , 'ปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 149, 134  , iconv( 'UTF-8','cp874' , 'ผิดปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 167, 134  , iconv( 'UTF-8','cp874' , 'ไม่ส่งตรวจ' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 130, 141  , iconv( 'UTF-8','cp874' , 'เม็ดเลือดแดง :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 150, 141 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "5060"){//เม็ดเลือดแดง Red Blood Cell
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 166, 141  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 166, 141  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 187, 141  , iconv( 'UTF-8','cp874' , 'cell/HPF' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 130, 148  , iconv( 'UTF-8','cp874' , 'พยาธิและไข่พยาธิ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 156, 148 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "5058"){//พยาธิและไข่พยาธิ Parasite or Ova
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 166, 148  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 166, 148  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 130, 155  , iconv( 'UTF-8','cp874' , 'Approved by :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 153, 155 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "5060"){//เม็ดเลือดแดง Red Blood Cell DSPNAME
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->DSPNAME == null){
                            $pdf->Text( 160, 155  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 160, 155  , iconv( 'UTF-8','cp874' , $datalab->DSPNAME ));
                        }
                    }
                }



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 130, 168  , iconv( 'UTF-8','cp874' , 'การตรวจเอกซเรย์ปอด' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 135, 175  , iconv( 'UTF-8','cp874' , 'ปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 149, 175  , iconv( 'UTF-8','cp874' , 'ผิดปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 167, 175  , iconv( 'UTF-8','cp874' , 'อื่นๆ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 182, 175  , iconv( 'UTF-8','cp874' , 'ไม่ส่งตรวจ' ));



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 130, 186  , iconv( 'UTF-8','cp874' , 'การตรวจมะเร็งปากมดลูด' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 135, 193  , iconv( 'UTF-8','cp874' , 'ปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 149, 193  , iconv( 'UTF-8','cp874' , 'ผิดปกติ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 167, 193  , iconv( 'UTF-8','cp874' , 'อื่นๆ' ));
                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 182, 193  , iconv( 'UTF-8','cp874' , 'ไม่ส่งตรวจ' ));



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 130, 204  , iconv( 'UTF-8','cp874' , 'การตรวจไทรอยด์ - พาราไทรอยด์' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 130, 211  , iconv( 'UTF-8','cp874' , 'TSH :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 139, 211 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "8012"){//TSH
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 150, 211  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 150, 211  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 172, 211 , iconv( 'UTF-8','cp874' , 'mIU/L (0.350-4.940)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 130, 218  , iconv( 'UTF-8','cp874' , 'FT3 :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 139, 218 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "8013"){//FT3
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 150, 218  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 150, 218  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 172, 218 , iconv( 'UTF-8','cp874' , 'pg/mL (1.58-3.91)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 130, 225  , iconv( 'UTF-8','cp874' , 'FT4 :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 139, 225 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "8014"){//FT4
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 150, 225  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 150, 225  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 172, 225 , iconv( 'UTF-8','cp874' , 'ng/dL (0.70-1.48)' ));



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 11, 238  , iconv( 'UTF-8','cp874' , 'การตรวจมะเร็ง' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 245  , iconv( 'UTF-8','cp874' , 'ตรวจหาสารบ่งชี้มะเร็งลำไส้ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 49, 245 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "2051"){//ตรวจหาสารบ่งชี้มะเร็งลำไส้ CEA
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 58 , 245  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 58 , 245  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 245  , iconv( 'UTF-8','cp874' , 'ng/mL (0.00-5.00)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 252  , iconv( 'UTF-8','cp874' , 'ตรวจหาสารบ่งชี้มะเร็งตับอ่อนและท่อน้ำดี :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 67, 252 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "2053"){//ตรวจหาสารบ่งชี้มะเร็งตับอ่อนและท่อน้ำดี CA19-9
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 68, 252  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 68, 252  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 252  , iconv( 'UTF-8','cp874' , 'U/mL (0.00-37.00)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 259  , iconv( 'UTF-8','cp874' , 'ตรวจหาสารบ่งชี้มะเร็งตับ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 47, 259 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "2050"){//ตรวจหาสารบ่งชี้มะเร็งตับ AFP
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 58, 259  , iconv( 'UTF-8','cp874' , ""));
                        }else{
                            $pdf->Text( 58, 259  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 259  , iconv( 'UTF-8','cp874' , 'IU/mL (0.00-8.78)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 266  , iconv( 'UTF-8','cp874' , 'ตรวจหาสารบ่งชี้มะเร็งต่อมลูกหมาก :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 59, 266 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "2054"){//ตรวจหาสารบ่งชี้มะเร็งต่อมลูกหมาก PSA
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 64, 266  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 64, 266  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 266  , iconv( 'UTF-8','cp874' , 'ng/mL (0.00-4.00)' ));
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 11, 273  , iconv( 'UTF-8','cp874' , 'ตรวจหาสารบ่งชี้มะเร็งรังไข่ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 49, 273 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _  ' ));
                foreach($APILAB->json_data as $datalab){
                    if($datalab->LABEXM == "2052"){//ตรวจหาสารบ่งชี้มะเร็งรังไข่ CA125
                        $pdf->SetFont('THSarabunNew','B',14);
                        if($datalab->RESULT == null){
                            $pdf->Text( 58, 273  , iconv( 'UTF-8','cp874' , "" ));
                        }else{
                            $pdf->Text( 58, 273  , iconv( 'UTF-8','cp874' , $datalab->RESULT ));
                        }
                    }
                }
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Text( 76, 273  , iconv( 'UTF-8','cp874' , 'U/mL (0.00-35.00)' ));



                $pdf->SetFont('THSarabunNew','B',14);
                $pdf->Text( 130, 238   , iconv( 'UTF-8','cp874' , 'ความเห็นของแพทย์' ));



                $pdf->SetFont('THSarabunNew','B',16);
                $pdf->Text( 10 , 284 , iconv( 'UTF-8','cp874' , 'ลงชื่อแพทย์ผู้ตรวจ :' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 43 , 285 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                $pdf->SetFont('THSarabunNew','B',16);
                $pdf->Text( 120 , 284 , iconv( 'UTF-8','cp874' , 'วันที่' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 129 , 285 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _  ' ));
                $pdf->SetFont('THSarabunNew','B',16);
                $pdf->Text( 160 , 284 , iconv( 'UTF-8','cp874' , 'วันที่พิมพ์' ));
                $pdf->SetFont('THSarabunNew','',10);
                $pdf->Text( 176 , 285 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _  ' ));
                $pdf->SetFont('THSarabunNew','B',16);
                $pdf->Text( 179 , 284 , iconv( 'UTF-8','cp874' , $ToDayTH ));
                //==========================================================================================================================================



                ///========================================================= กำหนดเส้นขอบ =======================================================================
                $pdf->SetTopMargin(0); //คำสั่งที่ใช้สำหรับ กำหนดกั้นหน้ากระดาษนั้น ประกอบด้วย
                //SetMargins( 50,30,10 ); คำสั่งนี้ใช้สำหรับกำหนดกั้นหน้ากระดาษ ซึ่งจะต้องเรียกใช้งานก่อนคำสั่ง AddPage (คำสั่งเพิ่มหน้ากระดาษ) โดยค่าดีฟอลต์แล้ว กั้นหน้ากระดาษทั้งซ้าย ขวา บน ล่าง จะถูกกำหนดไว้ที่ 1 ซม.
                //SetLeftMargin( 50 ); ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านซ้าย ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่
                //SetRightMargin( 50 ); ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านขวา ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่
                //SetTopMargin( 50 );  ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านบน หรือจะเรียกว่ากั้นหัวกระดาษ ก็ได้ ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่
                $pdf->SetY(8);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 195  , 20 , iconv( 'UTF-8','cp874','' ),'B'); // (float w, float h, string txt [, mixed border [, string align [, boolean fill]]]) สำหรับพิมพ์ข้อความลงในเอกสาร pdf
                $pdf->SetY(8);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 195  , 38 , iconv( 'UTF-8','cp874','' ),'B');
                $pdf->SetY(8);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 195  , 46 , iconv( 'UTF-8','cp874','' ),'B');
                $pdf->SetY(8);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 195  , 64 , iconv( 'UTF-8','cp874','' ),'B');
                $pdf->SetY(8);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 119  , 120 , iconv( 'UTF-8','cp874','' ),'B');
                $pdf->SetY(0);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(127);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 76  , 120 , iconv( 'UTF-8','cp874','' ),'B');

                //CBC
                $pdf->SetY(75);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(98);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(75);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(110);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');

                //การตรวจปัสสาวะ (Urine examination)
                $pdf->SetY(82);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(130);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(82);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(144);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(82);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(162);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');

                //สารเคมีในเลือด (Blood Chemistry)
                $pdf->SetY(130);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(98);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(130);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(110);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');

                //การตรวจอุจจาระ (Stool examination)
                $pdf->SetY(130);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(130);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(130);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(144);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(130);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(162);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(41);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(127);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 76  , 120 , iconv( 'UTF-8','cp874','' ),'B');

                $pdf->SetY(171);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(130);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(171);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(144);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(171);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(162);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(171);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(177);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(60);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(127);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 76  , 120 , iconv( 'UTF-8','cp874','' ),'B');

                $pdf->SetY(189);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(130);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(189);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(144);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(189);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(162);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(189);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(177);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(77);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(127);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 76  , 120 , iconv( 'UTF-8','cp874','' ),'B');


                $pdf->SetY(72);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(127);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 195  , 160 , iconv( 'UTF-8','cp874','' ),'L');
                $pdf->SetY(112);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 119  , 120 , iconv( 'UTF-8','cp874','' ),'B');
                $pdf->SetY(112);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(127);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 76  , 120 , iconv( 'UTF-8','cp874','' ),'B');
                $pdf->SetY(232);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(127);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 195  , 45 , iconv( 'UTF-8','cp874','' ),'L');
                $pdf->SetY(8);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 195  , 280 , iconv( 'UTF-8','cp874','' ),'LTRB');
                $pdf->SetY(8);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell( 195  , 269 , iconv( 'UTF-8','cp874','' ),'B');
                //^====================== MultiCell =========================
                //w : (ตัวเลข) ความกว้างของกล่องข้อความ ถ้าระบุเป็น 0 กล่องจะกว้างไปจนถึงกั้นขวาของกระดาษ
                //h : (ตัวเลข) ความสูงของกล่องข้อความ       //txt : (ตัวหนังสือ) ข้อความที่ต้องการพิมพ์
                //^======================= border ===========================
                //L: ขอบซ้าย        //T: ขอบบน      //R: ขอบขวา        //B: ขอบล่าง
                //^======================== align  ===========================
                //L or ค่าว่าง : ชิดซ้าย (default value)        //C: จัดกึ่งกลาง        //R: ชิดขวา
                //^========================= fill  ============================
                //false : ไม่แรเงา (default)        //true : แรเงา
                //===========================================================================================================================================

                $pdf->Output();
            }else{
                Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
                return $this->redirect(['login/login_his']);
            }
        }

    }

    public function API_checkup_queue($SDATE,$EDATE,$LRFRLCT,$HNID){
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        $curl = curl_init();
        $DataToken = 'SDATE='.$SDATE.'&EDATE='.$EDATE.'&LRFRLCT='.$LRFRLCT.'&HN_ID='.$HNID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjhcr_api/checkup_queue',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Access_Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_checkup_labgroup($SDATE,$EDATE,$LRFRLCT){
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        $curl = curl_init();
        $DataToken = 'SDATE='.$SDATE.'&EDATE='.$EDATE.'&LRFRLCT='.$LRFRLCT;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjhcr_api/checkup_labgroup',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Access_Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_checkup_hnall($HNID,$LRFRLCT){
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        $curl = curl_init();
        $DataToken = 'HN_ID='.$HNID.'&LRFRLCT='.$LRFRLCT;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjhcr_api/checkup_hnall',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Access_Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_checkup_hnalllabgroup($HNID,$LRFRLCT){
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        $curl = curl_init();
        $DataToken = 'HN_ID='.$HNID.'&LRFRLCT='.$LRFRLCT;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjhcr_api/checkup_hnalllabgroup',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Access_Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_checkup_lab($ADATE,$HNID,$LRFRLCT){
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        $curl = curl_init();
        $DataToken = 'ADATE='.$ADATE.'&HN_ID='.$HNID.'&LRFRLCT='.$LRFRLCT;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjhcr_api/checkup_lab',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Access_Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_checkup_lrfrlct(){
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjhcr_api/checkup_lrfrlct',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Access_Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_stack_download($TDATE,$ASTAFF,$LRFRLCT){
        $DataToken = 'TDATE='.$TDATE.'&ASTAFF='.$ASTAFF.'&LRFRLCT='.$LRFRLCT;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjhcr_api/stack_download',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }

}