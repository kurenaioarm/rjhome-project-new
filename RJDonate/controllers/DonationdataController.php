<?php
namespace  RJDonate\controllers;

use RJDonate\models\AskDonationsForm;
use RJDonate\models\StatuserForm;
use RJDonate\models\LoginForm;
use \yii\web\Controller;
use yii\helpers\Html;
use Yii;

class DonationdataController extends Controller
{

    public function actionIndex()
    {
        //=============== การใช้ session=================
        //Yii::$app->session->get('access_token'); //session นำมาใช้
        //Yii::$app->session->set('access_token'); //session เก็บ
        //=========================================

        $StatuserModel = new StatuserForm();

        //===================== เรียกใช้ session arrayAccess_Token  ========================
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        if ($Access_Token == null){
            Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
            return $this->redirect(['login/login_his']);
        }
        //======================== API ESI Check Token ==============================
        $API = $this->API_donate_home("", "",$Access_Token['access_token']);
        //=========================================================================
        //========================API คำนำหน้าชื่อ pname_api =============================
        $API_pname_api = $this->API_pname_api($Access_Token['access_token']);
        //========================API จังหวัดทั้งหมด CHANGWAT_ID ======================
        $API_changwat_api = $this->API_province_area_api('','','CHANGWAT_ALL',$Access_Token['access_token']);
        //=========================================================================


        //============ ตรวจสอบ session arrayAccess_Token กรณีมีการล้างค่า เป็น NULL  ===============
        if($API->json_result === false){
            Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
            return $this->redirect(['login/login_his']);
        }else{
            //============================================== สร้าง Array คำนำหน้าชื่อ =======================================================
            $Array_pname_api = array();
            if (isset($API_pname_api)) {
                foreach ($API_pname_api->json_data as $data_pname_api){
                    $DataID_Pname =  $data_pname_api->PNAME;
                    $DataNAME_Pname =  $data_pname_api->NAME ;
                    $Array_pname_api[$DataID_Pname]=$DataNAME_Pname; //การสร้าง Array แบบกำหนด  key value
                }
            }
            //============================================== สร้าง Array จังหวัดทั้งหมด =======================================================
            $Array_changwat_api = array();
            if (isset($API_changwat_api)) {
                foreach ($API_changwat_api->json_data as $data_changwat_api){
                    $DataID_Changwat =  $data_changwat_api->CHANGWAT;
                    $DataNAME_Changwat =  $data_changwat_api->CNAME ;
                    $Array_changwat_api[$DataID_Changwat]=$DataNAME_Changwat; //การสร้าง Array แบบกำหนด  key value
                }
            }

            if(Yii::$app->request->post('Check-button') != 5){
                if(Yii::$app->request->post('Check-button') != 4){
                    if(Yii::$app->request->post('Check-button') != 3){
                        if(Yii::$app->request->post('Check-button') != 2){
                            if(Yii::$app->request->post()) {

                                $StatuserModel->load(Yii::$app->request->post());
                                $SelectedDate = $StatuserModel->SelectedDate;
                                if($SelectedDate == ""){ // Check วันที่ ส่งมา
                                    Yii::$app->session->setFlash('error', 'กรุณาระบุวันที่');
                                }else {
                                    $SelectedDateCut = explode(" - ", $SelectedDate);
                                    $dateS = $SelectedDateCut[0];
                                    $dateE = $SelectedDateCut[1];

                                    $datecutS = explode( "/",$dateS);
                                    $datecutE = explode( "/",$dateE);
                                    $dateSV2 = $datecutS[2]."-".$datecutS[1]."-".$datecutS[0];
                                    $dateEV2 = $datecutE[2]."-".$datecutE[1]."-".$datecutE[0];

                                    //================= แปลงวันที่่ไทย ===============
                                    $SDateThai = Yii::$app->helper->dateThaiFull($dateSV2); // แปลงวันที่่ไทย
                                    $EDateThai = Yii::$app->helper->dateThaiFull($dateEV2); // แปลงวันที่่ไทย
                                    //=========================================

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
                                            //========================== API_donate_home =========================
                                            $API = $this->API_donate_home($dateS, $dateE,$Access_Token['access_token']);
                                            //============================================================
                                        }else{
                                            $API = null;
                                            Yii::$app->session->setFlash('error', 'ไม่สามารถตรวจสอบข้อมูลได้ <b><u>วันที่เลือกย้อนหลังต้องไม่เกิน 30 วัน</u></b> กรุณาระบุวันที่ใหม่อีกครั้ง');
                                        }
                                    }else{

                                    }
                                }
                            }
                        }else{
                            $this->API_donate_update(Yii::$app->request->post("DONATE_ID"),
                                Yii::$app->request->post("Name_Type".Yii::$app->request->post("DONATE_ID")),
                                Yii::$app->request->post("My_Pname".Yii::$app->request->post("DONATE_ID")),
                                Yii::$app->request->post("StatuserForm")["My_Name"],Yii::$app->request->post("StatuserForm")["My_Lname"],
                                Yii::$app->request->post("StatuserForm")["Taxpayer_Number"], Yii::$app->request->post("StatuserForm")["Address_No"],
                                Yii::$app->request->post("StatuserForm")["Alley"],Yii::$app->request->post("StatuserForm")["Road"],
                                Yii::$app->request->post("Tambon".Yii::$app->request->post("DONATE_ID")),
                                Yii::$app->request->post("Ampur".Yii::$app->request->post("DONATE_ID")),
                                Yii::$app->request->post("Changwat".Yii::$app->request->post("DONATE_ID")),
                                Yii::$app->request->post("Zipcode".Yii::$app->request->post("DONATE_ID")),
                                Yii::$app->request->post("StatuserForm")["Telephone"],Yii::$app->request->post("StatuserForm")["Donate_Note"],
                                Yii::$app->request->post("Letter_Type".Yii::$app->request->post("DONATE_ID")),
                                Yii::$app->request->post("Register_Type".Yii::$app->request->post("DONATE_ID")),
                                $Access_Token["user"]->staff,
                                $Access_Token['access_token']);
                            Yii::$app->session->setFlash('success', '<b>อัพเดทข้อมูลสำเร็จ</b>');
                            return $this->redirect(['donationdata/index']);
                        }
                    }
                }else{
                    $Current_Date = date("d/m/Y"); //วันที่ปัจจุบัน
                    $this->API_CONFIRM_INSERT(Yii::$app->request->post('Donate_Id'),Yii::$app->request->post('DT_Confirm'.Yii::$app->request->post('Donate_Id')),
                        Yii::$app->request->post('StatuserForm')['Telephone_Staff'],$Access_Token['user']->staff,$Current_Date,"CONFIRM_INSERT",$Access_Token['access_token']);
                    Yii::$app->session->setFlash('success', '<b>ทำการยืนยันการรับสิ่งของบริจาค เรียบร้อยแล้ว</b>');
                }
            }else{
                $this->API_CONFIRM_UPDATE(Yii::$app->request->post('Donate_Id'),Yii::$app->request->post('StatuserForm')['Telephone_Staff'],"CONFIRM_UPDATE",$Access_Token['access_token']);
                Yii::$app->session->setFlash('success', '<b>อัพเดทข้อมูลสำเร็จ</b>');
            }
        }

        return $this->render('index',[
            'model' => $StatuserModel,
            'Array_pname_api' => $Array_pname_api,
            'Array_changwat_api' => $Array_changwat_api,
            'Access_Token' => $Access_Token,
            'API' => $API
        ]);
    }

    public function actionSet_up_admin()
    {
        $LoginModel = new LoginForm();

        //============ ตรวจสอบ session arrayAccess_Token กรณีมีการล้างค่า เป็น NULL  ===============
        if(Yii::$app->session->get('arrayAccess_Token') === NULL){
            Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
            return $this->redirect(['login/login_his']);
        }else{
            //===================== เรียกใช้ session arrayAccess_Token  ========================
            $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
            //============================================ 1. API ADMIN =========================================================================
            $API_donate_admin = $this->API_donate_admin("","ADMIN_ALL",$Access_Token['access_token']);

            //======== ตรวจสอบ Token หมดอายุหรือยัง $API_donate_admin->json_result ==============
            if($API_donate_admin->json_result == true){
                if(Yii::$app->request->post()){
                    if(Yii::$app->request->post('Check-button') == 1){
                        $LoginModel->load(Yii::$app->request->post());
                        if($LoginModel->username == "" || $LoginModel->password == ""){
                            $LoginModel->validate();
                        }else{
                            $arr = array(
                                'user' => $LoginModel->username,
                                'pwd' => $LoginModel->password,
                            );
                            $jsonEncode = json_encode($arr);
                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/token/get_access_token',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                                CURLOPT_POSTFIELDS => $jsonEncode,

                                CURLOPT_HTTPHEADER => array(
                                    'Content-Type: application/json'
                                ),
                            ));
                            $response = curl_exec($curl);
                            $obj_access_token = json_decode($response);
                            curl_close($curl);

                            if($obj_access_token->json_result == true ){

                                //================= ถอดรหัส ====================
                                $currentToken = $obj_access_token->access_token;
                                $arrToken = explode('.', $currentToken);
                                $arrTokenDecode = array();
                                $arrTokenDecode['Header'] = $arrToken[0];
                                $arrTokenDecode['Payload'] = $arrToken[1];
                                $arrTokenDecode['Signature'] = $arrToken[2];
                                $payload = json_decode(base64_decode($arrTokenDecode['Payload']));
                                //===========================================

                                //============================ ตรวจสอบสิทธิ์ Admin ซ้ำ ===============================
                                $Check_Admin_id =  $this->API_donate_admin($payload->staff_idcrd,"",$Access_Token['access_token']);
                                if ($Check_Admin_id->json_total == null){
                                    //============================= เพิ่มสิทธิ์ Admin  =================================
                                    $this->API_add_donate_admin("","ADMIN_INSERT",$payload->staff_idcrd,$payload->staff_name,$payload->staff_lct,Yii::$app->request->post("LoginForm")["Permission_level"],$Access_Token['access_token']);
                                    Yii::$app->session->setFlash('success', '<b>ทำการเพิ่มสิทธิ์ Admin สำเร็จแล้ว</b>');
                                    return $this->redirect(['donationdata/set_up_admin']);
                                }else{
                                    Yii::$app->session->setFlash('error',   'มีสิทธิ์ Admin อยู่แล้ว ไม่สามารถเพิ่มได้อีก');
                                }
                                //=============================================================================
                            }else{
                                Yii::$app->session->setFlash('error',   'Username/Password ที่ใช้ลงทะเบียนไม่ถูกต้อง');
                            }
                        }
                    }else if(Yii::$app->request->post('Check-button') == 2){
                        $Check_Admin_id =  $this->API_donate_admin(Yii::$app->request->post("LoginForm")["Idcard"],"",$Access_Token['access_token']);
//                        var_dump($Check_Admin_id->json_total);die();
                        if ($Check_Admin_id->json_total == 0){
                            //============================= เช็ค Idcard ว่ามีใน HIS ไหม  =================================
                            $API_staff_name = $this->API_staff_name("",Yii::$app->request->post("LoginForm")["Idcard"],$Access_Token['access_token']);
                            if($API_staff_name->json_total == 0){
                                Yii::$app->session->setFlash('error',   'ไม่พบข้อมูล ในระบบ HIS กรุณาตรวจสอบ');
                            }else{
                                //============================= เพิ่มสิทธิ์ Admin  =================================
                                $this->API_add_donate_admin("","ADMIN_INSERT",$API_staff_name->json_data[0]->IDCRD,$API_staff_name->json_data[0]->DSPNAME,$API_staff_name->json_data[0]->LCT,Yii::$app->request->post("LoginForm")["Permission_level2"],$Access_Token['access_token']);
                                Yii::$app->session->setFlash('success', '<b>ทำการเพิ่มสิทธิ์ Admin สำเร็จแล้ว</b>');
                                return $this->redirect(['donationdata/set_up_admin']);
                            }
                        }else{
                            Yii::$app->session->setFlash('error',   'มีสิทธิ์ Admin อยู่แล้ว ไม่สามารถเพิ่มได้อีก');
                        }
                    }
                }
            }else{
                Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
                return $this->redirect(['login/login_his']);
            }
        }

        return $this->render('set_up_admin',[
            'model' => $LoginModel,
            'API_donate_admin' => $API_donate_admin
        ]);
    }

    public function actionAdmin_delete()
    {
        //===================== เรียกใช้ session arrayAccess_Token  ========================
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        //========================== เรียกใช้ Admin_delete  ==============================
        $this->API_donate_admin(Yii::$app->request->post("User")["ADMIN_ID"],"ADMIN_DELETE",$Access_Token['access_token']);
        Yii::$app->session->setFlash('success', '<b>ทำการลบสิทธิ์เรียบร้อยแล้วแล้ว</b>');
        return $this->redirect(['donationdata/set_up_admin']);
    }

    public function actionFpdfreport()
    {
        //===================== เรียกใช้ session arrayAccess_Token  ========================
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        date_default_timezone_set('Asia/Bangkok');
        require(dirname(__FILE__).'/../../web/assets/vendor_add_on/FPDF/fpdf.php');

        //=========================== API DONATEITEM / STAFF =========================
        $APIDONATEITEM = $this->API_donateitem_fpdf(Yii::$app->request->post("User")["DONATE_ID"],$Access_Token['access_token']);
        $APIDONATEITEMDT = $this->API_donateitemdt_fpdf(Yii::$app->request->post("User")["DONATE_ID"],$Access_Token['access_token']);
        $APISTAFF = $this->API_staff_name("",$Access_Token['admin']->json_data[0]->ADMIN_ID,$Access_Token['access_token']);
        //=============================================================================

        if($APIDONATEITEM->json_result == true){
            //=========================== API DONATEITEM =========================
            $APIPNAME = $this->API_pname2_api($APIDONATEITEM->json_data[0]->PNAME,$Access_Token['access_token']);
            //============================= วัน/เดือน/ปี ปัจจุบัน =============================

            $API_DONATE_CONFIRM_ID = $this->API_DONATE_CONFIRM_ID(Yii::$app->request->post("User")["DONATE_ID"],"",$Access_Token['access_token']);

            if( $API_DONATE_CONFIRM_ID->json_data == null){
                $ToDayTH0 = " ";
            }else{
                $ToDay2 = $API_DONATE_CONFIRM_ID->json_data[0]->CON_DATEM;
                $ToDayTH0 = Yii::$app->helper->dateThaiFull($ToDay2); // แปลงวันที่่ไทย
            }

            if($API_DONATE_CONFIRM_ID->json_data == null){
                $API_CON_TELEPHONE  = " ";
            }else{
                $API_CON_TELEPHONE = $API_DONATE_CONFIRM_ID->json_data[0]->CON_TELEPHONE;
            }

            $ToDay_Not_Time = explode( " ",$APIDONATEITEM->json_data[0]->DONATE_DATE);
            $ToDayCut = explode( "/",$ToDay_Not_Time[0]);
            $ToDayTH = $ToDayCut[0]."/".$ToDayCut[1]."/".$ToDayCut[2];
            if($ToDayCut[1] == "01"){
                $TMONTH = "มกราคม";
            }else if($ToDayCut[1] == "02"){
                $TMONTH = "กุมภาพันธ์";
            }else if($ToDayCut[1] == "03"){
                $TMONTH = "มีนาคม";
            }else if($ToDayCut[1] == "04"){
                $TMONTH = "เมษายน";
            }else if($ToDayCut[1] == "05"){
                $TMONTH = "พฤษภาคม";
            }else if($ToDayCut[1] == "06"){
                $TMONTH = "มิถุนายน";
            }else if($ToDayCut[1] == "07"){
                $TMONTH = "กรกฎาคม";
            }else if($ToDayCut[1] == "08"){
                $TMONTH = "สิงหาคม";
            }else if($ToDayCut[1] == "09"){
                $TMONTH = "กันยายน";
            }else if($ToDayCut[1] == "10"){
                $TMONTH = "ตุลาคม";
            }else if($ToDayCut[1] == "11"){
                $TMONTH = "พฤศจิกายน";
            }else if($ToDayCut[1] == "12"){
                $TMONTH = "ธันวาคม";
            }
            //=======================================================================
            $Total_Money =  number_format(0,2) ;
            //========================================================== ตั่งค่าหน้ากระดาษ =====================================================================
            $pdf=new \FPDF( 'P' , 'mm' , 'A4' ); //(orientation, 'mm' , format)  สร้าง instant FPDF
            //^==================== orientation ==========================
            //P – แนวตั้ง (default)     //L – แนวนอน
            //^====================== format ============================
            //A3        //A4 (default)      //A5        //Letter        //array(width,height) – กำหนดเอง โดยส่งอะเรย์ กว้างxสูง
            $pdf->SetAuthor( 'RajavithiDONATE' ); //กำหนดชื่อเจ้าของเอกสาร
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
            $pdf->SetTitle( 'Donate Report' ); //สำหรับกำหนด title ของเอกสาร
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
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 26 , iconv( 'UTF-8','cp874' , 'งาน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 40 , 26 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 30, 12 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
            $pdf->Cell( 30, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 32, 5 , iconv( 'UTF-8','cp874' , $APISTAFF->json_data[0]->WRKDIVNM) , 0, 0 , 'C'  );
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 73, 26 , iconv( 'UTF-8','cp874' , 'กลุ่มงาน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 85 , 26 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 13, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 40, 5 , iconv( 'UTF-8','cp874' , $APISTAFF->json_data[0]->DIVNAME) , 0, 0 , 'C'  );
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 127, 26 , iconv( 'UTF-8','cp874' , 'วันที่' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 133 , 26 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 6, 5 , iconv( 'UTF-8','cp874' , $ToDayCut[0]) , 0, 0 , 'C'  );
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 139, 26 , iconv( 'UTF-8','cp874' , 'เดือน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 147 , 26 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
            if (isset($TMONTH)) {
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 18, 5 , iconv( 'UTF-8','cp874' , $TMONTH) , 0, 0 , 'C'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 165, 26 , iconv( 'UTF-8','cp874' , 'พ.ศ.' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 172 , 26 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 5, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 13, 5 , iconv( 'UTF-8','cp874' , $ToDayCut[2]) , 0, 1 , 'C'  );



            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 36 , iconv( 'UTF-8','cp874' , 'เรื่อง' ));
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 36 , iconv( 'UTF-8','cp874' , 'ขอบริจาคสิ่งของ' ));


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 44 , iconv( 'UTF-8','cp874' , 'เรียน' ));
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 44 , iconv( 'UTF-8','cp874' , 'ผู้อำนวยการโรงพยาบาลราชวิถี' ));


            if(Yii::$app->request->post("User")["NAME_TYPE"] == "1"){
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 44, 52 , iconv( 'UTF-8','cp874' , 'ข้าพเจ้า' ));
                $pdf->SetFont('THSarabunNew','B',5);
                $pdf->Text( 55 , 52 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
                $pdf->SetFont('THSarabunNew','',14);
                if($APIPNAME->json_data[0]->PNAME == "999"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 45, 21 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 45, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 130, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->NAME ." ". $APIDONATEITEM->json_data[0]->LNAME) , 0, 1 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 45, 21 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 45, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 130, 5 , iconv( 'UTF-8','cp874' , $APIPNAME->json_data[0]->NAME ." ". $APIDONATEITEM->json_data[0]->NAME ." ". $APIDONATEITEM->json_data[0]->LNAME) , 0, 1 , 'L'  );
                }
            }else{
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 44, 52 , iconv( 'UTF-8','cp874' , 'ข้าพเจ้า' ));
                $pdf->SetFont('THSarabunNew','B',5);
                $pdf->Text( 55 , 52 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                if($APIPNAME->json_data[0]->PNAME == "999"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 45, 21 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 45, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 75, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->NAME) , 0, 0 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 45, 21 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 45, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 75, 5 , iconv( 'UTF-8','cp874' , $APIPNAME->json_data[0]->NAME ." ". $APIDONATEITEM->json_data[0]->NAME) , 0, 0 , 'L'  );
                }
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 130, 52 , iconv( 'UTF-8','cp874' , 'เลขผู้เสียภาษี' ));
                $pdf->SetFont('THSarabunNew','B',5);
                $pdf->Text( 149 , 52 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
                if($APIDONATEITEM->json_data[0]->TAXPAYER_NUMBER == "0"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 19, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 35, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 19, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 35, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->TAXPAYER_NUMBER) , 0, 1 , 'C'  );
                }
            }


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 59 , iconv( 'UTF-8','cp874' , 'ที่อยู่ เลขที่' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 50 , 59 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if($APIDONATEITEM->json_data[0]->ADDRESS_NO == null){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 43, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 43, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->ADDRESS_NO) , 0, 0 , 'C'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 92, 59 , iconv( 'UTF-8','cp874' , 'ซอย' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 98 , 59 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if($APIDONATEITEM->json_data[0]->ALLEY == null){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 6, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 6, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->ALLEY) , 0, 0 , 'C'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 137, 59 , iconv( 'UTF-8','cp874' , 'ถนน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 144 , 59 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if($APIDONATEITEM->json_data[0]->ROAD == null){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 7, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 7, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->ROAD) , 0, 1 , 'C'  );
            }


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 66 , iconv( 'UTF-8','cp874' , 'ตำบล/แขวง' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 52 , 66 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
            if($APIDONATEITEM->json_data[0]->TNAME == null){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 41, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 41, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 41, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 41, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 41, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 41, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->TNAME) , 0, 0 , 'C'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 92, 66 , iconv( 'UTF-8','cp874' , 'อำเภอ/เขต' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 108 , 66 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if($APIDONATEITEM->json_data[0]->DNAME == null){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 16, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 29, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 16, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 29, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->DNAME) , 0, 0 , 'C'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 137, 66 , iconv( 'UTF-8','cp874' , 'จังหวัด' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 147 , 66 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if($APIDONATEITEM->json_data[0]->PRNAME == null){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 10, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 36, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 10, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 36, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->PRNAME) , 0, 1 , 'C'  );
            }


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 73 , iconv( 'UTF-8','cp874' , 'รหัสไปรษณีย์' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 53 , 73 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
            if($APIDONATEITEM->json_data[0]->ZIP_CODE == null){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 43, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 43, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 43, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 43, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->ZIP_CODE) , 0, 0 , 'C'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 92, 73 , iconv( 'UTF-8','cp874' , 'โทรศัพท์' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 105 , 73 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if($APIDONATEITEM->json_data[0]->TELEPHONE == "0"){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 12, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 33, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 12, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 33, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->TELEPHONE) , 0, 1 , 'C'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 137, 73 , iconv( 'UTF-8','cp874' , 'ขอบริจาคสิ่งของ ดังนี้' ));


            //------------------------------------------------------------------------------------------ รายการสิ่งของบริจาค ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//1-----------------------------------------------
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 80 , iconv( 'UTF-8','cp874' , '๑.' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 49 , 80 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[0]->ITEM_NAME)){
                if($APIDONATEITEMDT->json_data[0]->ITEM_NAME == "อื่นๆ (ระบุ)"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[0]->ITEM_NAMEOTHER) , 0, 1 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[0]->ITEM_NAME) , 0, 1 , 'L'  );
                }
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 49, 87 , iconv( 'UTF-8','cp874' , 'จำนวน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 60 , 87 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[0]->QUANTITY)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , number_format( $APIDONATEITEMDT->json_data[0]->QUANTITY )) , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }
            if(isset($APIDONATEITEMDT->json_data[0]->CU_NAME)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[0]->CU_NAME) , 0, 0 , 'L'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 105, 87 , iconv( 'UTF-8','cp874' , 'มูลค่า' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113 , 87 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[0]->PRICE)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "-- ".number_format( $APIDONATEITEMDT->json_data[0]->PRICE , 2 ) ) , 0 , 1 , 'R'  );
                $Total_Money = $Total_Money + ($APIDONATEITEMDT->json_data[0]->PRICE * $APIDONATEITEMDT->json_data[0]->QUANTITY);
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "" ) , 0 , 1 , 'R'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 142, 87 , iconv( 'UTF-8','cp874' , 'บาท' ));

//2-----------------------------------------------
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 93 , iconv( 'UTF-8','cp874' , '๒.' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 49 , 93 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[1]->ITEM_NAME)){
                if($APIDONATEITEMDT->json_data[1]->ITEM_NAME == "อื่นๆ (ระบุ)"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[1]->ITEM_NAMEOTHER) , 0, 1 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[1]->ITEM_NAME) , 0, 1 , 'L'  );
                }
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 49, 100 , iconv( 'UTF-8','cp874' , 'จำนวน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 60 , 100 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[1]->QUANTITY)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , number_format( $APIDONATEITEMDT->json_data[1]->QUANTITY )) , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }
            if(isset($APIDONATEITEMDT->json_data[1]->CU_NAME)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[1]->CU_NAME) , 0, 0 , 'L'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 105, 100 , iconv( 'UTF-8','cp874' , 'มูลค่า' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113 , 100 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[1]->PRICE)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "-- ".number_format( $APIDONATEITEMDT->json_data[1]->PRICE , 2 ) ) , 0 , 1 , 'R'  );
                $Total_Money = $Total_Money + ($APIDONATEITEMDT->json_data[1]->PRICE * $APIDONATEITEMDT->json_data[1]->QUANTITY);
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "" ) , 0 , 1 , 'R'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 142, 100 , iconv( 'UTF-8','cp874' , 'บาท' ));

//3-----------------------------------------------
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 106 , iconv( 'UTF-8','cp874' , '๓.' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 49 , 106 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[2]->ITEM_NAME)){
                if($APIDONATEITEMDT->json_data[2]->ITEM_NAME == "อื่นๆ (ระบุ)"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[2]->ITEM_NAMEOTHER) , 0, 1 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[2]->ITEM_NAME) , 0, 1 , 'L'  );
                }
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 49, 113 , iconv( 'UTF-8','cp874' , 'จำนวน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 60 , 113 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[2]->QUANTITY)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , number_format( $APIDONATEITEMDT->json_data[2]->QUANTITY )) , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }
            if(isset($APIDONATEITEMDT->json_data[2]->CU_NAME)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[2]->CU_NAME) , 0, 0 , 'L'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 105, 113 , iconv( 'UTF-8','cp874' , 'มูลค่า' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113 , 113 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[2]->PRICE)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "-- ".number_format( $APIDONATEITEMDT->json_data[2]->PRICE , 2 ) ) , 0 , 1 , 'R'  );
                $Total_Money = $Total_Money + ($APIDONATEITEMDT->json_data[2]->PRICE * $APIDONATEITEMDT->json_data[2]->QUANTITY);
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "" ) , 0 , 1 , 'R'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 142, 113 , iconv( 'UTF-8','cp874' , 'บาท' ));

//4-----------------------------------------------
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 119 , iconv( 'UTF-8','cp874' , '๔.' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 49 , 119 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[3]->ITEM_NAME)){
                if($APIDONATEITEMDT->json_data[3]->ITEM_NAME == "อื่นๆ (ระบุ)"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[3]->ITEM_NAMEOTHER) , 0, 1 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[3]->ITEM_NAME) , 0, 1 , 'L'  );
                }
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 49, 126 , iconv( 'UTF-8','cp874' , 'จำนวน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 60 , 126 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[3]->QUANTITY)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , number_format( $APIDONATEITEMDT->json_data[3]->QUANTITY )) , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }
            if(isset($APIDONATEITEMDT->json_data[3]->CU_NAME)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[3]->CU_NAME) , 0, 0 , 'L'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 105, 126 , iconv( 'UTF-8','cp874' , 'มูลค่า' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113 , 126 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[3]->PRICE)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "-- ".number_format( $APIDONATEITEMDT->json_data[3]->PRICE , 2 ) ) , 0 , 1 , 'R'  );
                $Total_Money = $Total_Money + ($APIDONATEITEMDT->json_data[3]->PRICE * $APIDONATEITEMDT->json_data[3]->QUANTITY);
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "" ) , 0 , 1 , 'R'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 142, 126 , iconv( 'UTF-8','cp874' , 'บาท' ));

//5-----------------------------------------------
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 132 , iconv( 'UTF-8','cp874' , '๕.' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 49 , 132 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[4]->ITEM_NAME)){
                if($APIDONATEITEMDT->json_data[4]->ITEM_NAME == "อื่นๆ (ระบุ)"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[4]->ITEM_NAMEOTHER) , 0, 1 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[4]->ITEM_NAME) , 0, 1 , 'L'  );
                }
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 49, 139 , iconv( 'UTF-8','cp874' , 'จำนวน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 60 , 139 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[4]->QUANTITY)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , number_format( $APIDONATEITEMDT->json_data[4]->QUANTITY )) , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }
            if(isset($APIDONATEITEMDT->json_data[4]->CU_NAME)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[4]->CU_NAME) , 0, 0 , 'L'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 105, 139 , iconv( 'UTF-8','cp874' , 'มูลค่า' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113 , 139 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[4]->PRICE)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "-- ".number_format( $APIDONATEITEMDT->json_data[4]->PRICE , 2 ) ) , 0 , 1 , 'R'  );
                $Total_Money = $Total_Money + ($APIDONATEITEMDT->json_data[4]->PRICE * $APIDONATEITEMDT->json_data[4]->QUANTITY);
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "" ) , 0 , 1 , 'R'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 142, 139 , iconv( 'UTF-8','cp874' , 'บาท' ));

//6-----------------------------------------------
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 145 , iconv( 'UTF-8','cp874' , '๖.' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 49 , 145 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[5]->ITEM_NAME)){
                if($APIDONATEITEMDT->json_data[5]->ITEM_NAME == "อื่นๆ (ระบุ)"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[5]->ITEM_NAMEOTHER) , 0, 1 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[5]->ITEM_NAME) , 0, 1 , 'L'  );
                }
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 49, 152 , iconv( 'UTF-8','cp874' , 'จำนวน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 60 , 152 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[5]->QUANTITY)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , number_format( $APIDONATEITEMDT->json_data[5]->QUANTITY )) , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }
            if(isset($APIDONATEITEMDT->json_data[5]->CU_NAME)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[5]->CU_NAME) , 0, 0 , 'L'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 105, 152 , iconv( 'UTF-8','cp874' , 'มูลค่า' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113 , 152 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[5]->PRICE)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "-- ".number_format( $APIDONATEITEMDT->json_data[5]->PRICE , 2 ) ) , 0 , 1 , 'R'  );
                $Total_Money = $Total_Money + ($APIDONATEITEMDT->json_data[5]->PRICE * $APIDONATEITEMDT->json_data[5]->QUANTITY);
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "" ) , 0 , 1 , 'R'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 142, 152 , iconv( 'UTF-8','cp874' , 'บาท' ));

//7-----------------------------------------------
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 158 , iconv( 'UTF-8','cp874' , '๗.' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 49 , 158 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','B',15);
            if(isset($APIDONATEITEMDT->json_data[6]->ITEM_NAME)){
                if($APIDONATEITEMDT->json_data[6]->ITEM_NAME == "อื่นๆ (ระบุ)"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[6]->ITEM_NAMEOTHER) , 0, 1 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[6]->ITEM_NAME) , 0, 1 , 'L'  );
                }
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 49, 165 , iconv( 'UTF-8','cp874' , 'จำนวน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 60 , 165 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[6]->QUANTITY)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , number_format( $APIDONATEITEMDT->json_data[6]->QUANTITY )) , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }
            if(isset($APIDONATEITEMDT->json_data[6]->CU_NAME)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[6]->CU_NAME) , 0, 0 , 'L'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 105, 165 , iconv( 'UTF-8','cp874' , 'มูลค่า' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113 , 165 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[6]->PRICE)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "-- ".number_format( $APIDONATEITEMDT->json_data[6]->PRICE , 2 ) ) , 0 , 1 , 'R'  );
                $Total_Money = $Total_Money + ($APIDONATEITEMDT->json_data[6]->PRICE * $APIDONATEITEMDT->json_data[6]->QUANTITY);
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "" ) , 0 , 1 , 'R'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 142, 165 , iconv( 'UTF-8','cp874' , 'บาท' ));

//8-----------------------------------------------
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 44, 171 , iconv( 'UTF-8','cp874' , '๘.' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 49 , 171 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[7]->ITEM_NAME)){
                if($APIDONATEITEMDT->json_data[7]->ITEM_NAME == "อื่นๆ (ระบุ)"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[7]->ITEM_NAMEOTHER) , 0, 1 , 'L'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[7]->ITEM_NAME) , 0, 1 , 'L'  );
                }
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 39, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 39, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 135, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 49, 178 , iconv( 'UTF-8','cp874' , 'จำนวน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 60 , 178 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[7]->QUANTITY)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , number_format( $APIDONATEITEMDT->json_data[7]->QUANTITY )) , 0, 0 , 'C'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 49, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 21, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            }
            if(isset($APIDONATEITEMDT->json_data[7]->CU_NAME)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEMDT->json_data[7]->CU_NAME) , 0, 0 , 'L'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 25, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 105, 178 , iconv( 'UTF-8','cp874' , 'มูลค่า' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113 , 178 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEMDT->json_data[7]->PRICE)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "-- ".number_format( $APIDONATEITEMDT->json_data[7]->PRICE , 2 ) ) , 0 , 1 , 'R'  );
                $Total_Money = $Total_Money + ($APIDONATEITEMDT->json_data[7]->PRICE * $APIDONATEITEMDT->json_data[7]->QUANTITY);
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 8, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 28, 5 , iconv( 'UTF-8','cp874' , "" ) , 0 , 1 , 'R'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 142, 178 , iconv( 'UTF-8','cp874' , 'บาท' ));


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 185 , iconv( 'UTF-8','cp874' , 'รวมเป็นเงินมูลค่าทั้งสิ้น' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 67 , 185 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 57, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
            $pdf->Cell( 57, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 24, 5 , iconv( 'UTF-8','cp874' , "-- ".number_format($Total_Money,2) ) , 0 , 0 , 'R'  );
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 91, 185 , iconv( 'UTF-8','cp874' , 'บาท' ));
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 100, 185 , iconv( 'UTF-8','cp874' , '(' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 102, 185 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _  ' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 91, 5 , iconv( 'UTF-8','cp874' , "- ".$this->Convert($Total_Money) ) , 0 , 1 , 'R'  );
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 182, 185 , iconv( 'UTF-8','cp874' , ')' ));


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 192 , iconv( 'UTF-8','cp874' , 'บริจาคเนื่องในโอกาส' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 64 , 192 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(isset($APIDONATEITEM->json_data[0]->DONATE_NOTE)){
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 54, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 54, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 120, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->DONATE_NOTE) , 0, 1 , 'L'  );
            }else{
                $pdf->SetFont('THSarabunNew','',14);
                $pdf->Cell( 54, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                $pdf->Cell( 54, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                $pdf->Cell( 120, 5 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'L'  );
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 58, 201 , iconv( 'UTF-8','cp874' , 'จึงเรียนมาเพื่อทราบ' ));


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 127, 208 , iconv( 'UTF-8','cp874' , 'ขอแสดงความนับถือ' ));


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 105, 216 , iconv( 'UTF-8','cp874' , '(ลงชื่อ)' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 115, 216 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            if(Yii::$app->request->post("User")["NAME_TYPE"] == "1"){
                if($APIPNAME->json_data[0]->PNAME == "999"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 105, 19 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 105, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 55, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->NAME ." ". $APIDONATEITEM->json_data[0]->LNAME) , 0, 1 , 'C'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 105, 19 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 105, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 55, 5 , iconv( 'UTF-8','cp874' , $APIPNAME->json_data[0]->NAME ." ". $APIDONATEITEM->json_data[0]->NAME ." ". $APIDONATEITEM->json_data[0]->LNAME) , 0, 1 , 'C'  );
                }
            }else{
                if($APIPNAME->json_data[0]->PNAME == "999"){
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 105, 19 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 105, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 55, 5 , iconv( 'UTF-8','cp874' , $APIDONATEITEM->json_data[0]->NAME) , 0, 1 , 'C'  );
                }else{
                    $pdf->SetFont('THSarabunNew','',14);
                    $pdf->Cell( 105, 19 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
                    $pdf->Cell( 105, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
                    $pdf->Cell( 55, 5 , iconv( 'UTF-8','cp874' , $APIPNAME->json_data[0]->NAME ." ". $APIDONATEITEM->json_data[0]->NAME) , 0, 1 , 'C'  );
                }
            }
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 170, 216 , iconv( 'UTF-8','cp874' , 'ผู้บริจาค' ));


            if($APIDONATEITEM->json_data[0]->LETTER_TYPE == "1"){
                $pdf->Image('../../web/assets/images/logo/CheckMark.png',38,215,10,0);
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 45, 223 , iconv( 'UTF-8','cp874' , 'ต้องการหนังสือตอบขอบคุณ' ));
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 100, 223 , iconv( 'UTF-8','cp874' , 'ไม่ต้องการหนังสือตอบขอบคุณ' ));
            }else{
                $pdf->Image('../../web/assets/images/logo/CheckMark.png',93,215,10,0);
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 45, 223 , iconv( 'UTF-8','cp874' , 'ต้องการหนังสือตอบขอบคุณ' ));
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 100, 223 , iconv( 'UTF-8','cp874' , 'ไม่ต้องการหนังสือตอบขอบคุณ' ));
            }

            if($APIDONATEITEM->json_data[0]->REGISTER_TYPE == "1"){
                $pdf->Image('../../web/assets/images/logo/CheckMark.png',38,221,10,0);
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 45, 229 , iconv( 'UTF-8','cp874' , 'ลงทะเบียนครุภัณฑ์' ));
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 100, 229 , iconv( 'UTF-8','cp874' , 'ไม่ลงทะเบียนครุภัณฑ์' ));
            }elseif ($APIDONATEITEM->json_data[0]->REGISTER_TYPE == "2"){
                $pdf->Image('../../web/assets/images/logo/CheckMark.png',93,221,10,0);
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 45, 229 , iconv( 'UTF-8','cp874' , 'ลงทะเบียนครุภัณฑ์' ));
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 100, 229 , iconv( 'UTF-8','cp874' , 'ไม่ลงทะเบียนครุภัณฑ์' ));
            }else{
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 45, 229 , iconv( 'UTF-8','cp874' , 'ลงทะเบียนครุภัณฑ์' ));
                $pdf->SetFont('THSarabunNew','',15);
                $pdf->Text( 100, 229 , iconv( 'UTF-8','cp874' , 'ไม่ลงทะเบียนครุภัณฑ์' ));
            }


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 239 , iconv( 'UTF-8','cp874' , 'สิ่งของบริจาคไว้ที่ งาน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 65, 239 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 55, 18 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
            $pdf->Cell( 55, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 49, 5 , iconv( 'UTF-8','cp874' , $APISTAFF->json_data[0]->WRKDIVNM ) , 0, 0 , 'C'  );
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 114, 239 , iconv( 'UTF-8','cp874' , 'กลุ่มงาน' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 126, 239 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 12, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 57, 5 , iconv( 'UTF-8','cp874' , $APISTAFF->json_data[0]->DIVNAME ) , 0, 1 , 'C'  );


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 34, 245 , iconv( 'UTF-8','cp874' , 'ซึ่งได้รับสิ่งของบริจาคตามจำนวนดังกล่าวเรียบร้อยแล้ว เมื่อวันที่' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 123, 245 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 100, 1 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
            $pdf->Cell( 112, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 37, 5 , iconv( 'UTF-8','cp874' , $ToDayTH0) , 0, 0 , 'C'  );
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 160, 245 , iconv( 'UTF-8','cp874' , 'โทร' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 166, 245 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 7, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 17, 5 , iconv( 'UTF-8','cp874' , $API_CON_TELEPHONE) , 0, 1 , 'C'  );


            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 103, 257 , iconv( 'UTF-8','cp874' , '(ลงชื่อ)' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113, 257 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 103, 7 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
            $pdf->Cell( 103, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 55, 5 , iconv( 'UTF-8','cp874' ,  $Access_Token["user"]->staff_name) , 0, 1 , 'C'  );
            $pdf->SetFont('THSarabunNew','',15);
            $pdf->Text( 168, 257 , iconv( 'UTF-8','cp874' , 'ผู้รับบริจาค' ));
            $pdf->SetFont('THSarabunNew','',15);


            $pdf->Text( 100, 264 , iconv( 'UTF-8','cp874' , 'ตำแหน่ง' ));
            $pdf->SetFont('THSarabunNew','B',5);
            $pdf->Text( 113, 264 , iconv( 'UTF-8','cp874' , '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _' ));
            $pdf->SetFont('THSarabunNew','',14);
            $pdf->Cell( 102, 2 , iconv( 'UTF-8','cp874' , "") , 0, 1 , 'C'  );
            $pdf->Cell( 102, 5 , iconv( 'UTF-8','cp874' , "") , 0, 0 , 'C'  );
            $pdf->Cell( 55, 5 , iconv( 'UTF-8','cp874' ,  $Access_Token["user"]->staff_posnm) , 0, 1 , 'C'  );


            //==========================================================================================================================================


            ///========================================================= กำหนดเส้นขอบ =======================================================================
            $pdf->SetTopMargin(0); //คำสั่งที่ใช้สำหรับ กำหนดกั้นหน้ากระดาษนั้น ประกอบด้วย
            //SetMargins( 50,30,10 ); คำสั่งนี้ใช้สำหรับกำหนดกั้นหน้ากระดาษ ซึ่งจะต้องเรียกใช้งานก่อนคำสั่ง AddPage (คำสั่งเพิ่มหน้ากระดาษ) โดยค่าดีฟอลต์แล้ว กั้นหน้ากระดาษทั้งซ้าย ขวา บน ล่าง จะถูกกำหนดไว้ที่ 1 ซม.
            //SetLeftMargin( 50 ); ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านซ้าย ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่
            //SetRightMargin( 50 ); ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านขวา ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่
            //SetTopMargin( 50 );  ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านบน หรือจะเรียกว่ากั้นหัวกระดาษ ก็ได้ ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่

            $pdf->SetY(72);//เลื่อนแกรน Y แนวตั้ง
            $pdf->SetX(34);//เลื่อนแกรน X แนวตนอน
            $pdf->SetFont('THSarabunNew','B',15);
            $pdf->MultiCell( 150  , 160 , iconv( 'UTF-8','cp874','' ),'BC');

            $pdf->SetY(219);//เลื่อนแกรน Y แนวตั้ง
            $pdf->SetX(40);//เลื่อนแกรน X แนวตนอน
            $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');

            $pdf->SetY(219);//เลื่อนแกรน Y แนวตั้ง
            $pdf->SetX(95);//เลื่อนแกรน X แนวตนอน
            $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');

            $pdf->SetY(225);//เลื่อนแกรน Y แนวตั้ง
            $pdf->SetX(40);//เลื่อนแกรน X แนวตนอน
            $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');

            $pdf->SetY(225);//เลื่อนแกรน Y แนวตั้ง
            $pdf->SetX(95);//เลื่อนแกรน X แนวตนอน
            $pdf->MultiCell( 4  , 4 , iconv( 'UTF-8','cp874','' ),'LTRB');

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



            //=================================================== Report 2 ================================================================
            $pdf->AddPage();//เพิ่มหน้ากระดาษ
            //=================================================== Medical Check Up Report แบบฟอร์มบริจาคหน่วยงานต่างๆ ================================================================


            $pdf->Output();
        }else{
            Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
            return $this->redirect(['login/login_his']);
        }
    }


    function Convert($amount_number)//ฟังก์ชั่นแปลงค่าเงินตัวเลขเป็นตัวอักษรภาษาไทย
    {
        $amount_number = number_format($amount_number, 2, ".","");
        $pt = strpos($amount_number , ".");
        $number = $fraction = "";
        if ($pt === false)
            $number = $amount_number;
        else
        {
            $number = substr($amount_number, 0, $pt);
            $fraction = substr($amount_number, $pt + 1);
        }

        $ret = "";
        $baht = $this->ReadNumber ($number);
        if ($baht != "")
            $ret .= $baht . "บาท";

        $satang = $this->ReadNumber($fraction);
        if ($satang != "")
            $ret .=  $satang . "สตางค์";
        else
            $ret .= "ถ้วน";
        return $ret;
    }
    function ReadNumber($number)//ฟังก์ชั่นแปลงค่าเงินตัวเลขเป็นตัวอักษรภาษาไทย
    {
        $position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
        $number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
        $number = $number + 0;
        $ret = "";
        if ($number == 0) return $ret;
        if ($number > 1000000)
        {
            $ret .= $this->ReadNumber(intval($number / 1000000)) . "ล้าน";
            $number = intval(fmod($number, 1000000));
        }

        $divider = 100000;
        $pos = 0;
        while($number > 0)
        {
            $d = intval($number / $divider);
            $ret .= (($divider == 10) && ($d == 2)) ? "ยี่" :
                ((($divider == 10) && ($d == 1)) ? "" :
                    ((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
            $ret .= ($d ? $position_call[$pos] : "");
            $number = $number % $divider;
            $divider = $divider / 10;
            $pos++;
        }
        return $ret;
    }

    public function actionCheckampur(){ //ค้นหา อำเภอ/เขต
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        //============================================= 1. API อำเภอทั้งหมด AMPUR_ID ================================================
        $API_ampur_api = $this->API_province_area_api('','',Yii::$app->request->post('Changwat_ID'),$Access_Token['access_token']);
        //============================================== สร้าง Array อำเภอทั้งหมด =======================================================
        $Array_ampur_api = array();
        $Array_changwat_api = "";
        if (isset($API_ampur_api)) {
            foreach ($API_ampur_api->json_data as $data_ampur_api){
                $DataID_Ampur =  $data_ampur_api->AMPUR;
                $DataNAME_Ampur =  $data_ampur_api->ANAME ;
                $Array_ampur_api[$DataID_Ampur]=$DataNAME_Ampur; //การสร้าง Array แบบกำหนด  key value
                $Array_changwat_api = $data_ampur_api->CHANGWAT;
            }
        }
        if(Yii::$app->request->post('Changwat_ID') == ""){
            $Check_Status = "";
        }else{
            $Check_Status = "1";
        }

        return $this->renderAjax('_Incident_Ampur', [ //อำเภอ/เขต
            'Array_ampur_api' => $Array_ampur_api,
            'Array_changwat_api' => $Array_changwat_api,
            'Check_Status' => $Check_Status,
            'DONATE_ID' => Yii::$app->request->post('DONATE_ID'),
            'DISTRICT' => "",
        ]);
    }

    public function actionChecktumbon(){ //ค้นหา ตำบล
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        //============================================ 1. API ตำบลทั้งหมด TUMBON_ID ========================================
        $API_tumbon_api = $this->API_province_area_api('',Yii::$app->request->post('Ampurstep2_ID'),Yii::$app->request->post('Changwatstep2_ID'),$Access_Token['access_token']);
        //============================================== สร้าง Array ตำบลทั้งหมด =======================================================
        $Array_tumbon_api = array();
        $Array_changwat_api = "";
        $Array_ampur_api = "";
        if (isset($API_tumbon_api)) {
            foreach ($API_tumbon_api->json_data as $data_tumbon_api){
                $DataID_Tumbon =  $data_tumbon_api->TUMBON;
                $DataNAME_Tumbon =  $data_tumbon_api->TNAME ;
                $Array_tumbon_api[$DataID_Tumbon]=$DataNAME_Tumbon; //การสร้าง Array แบบกำหนด  key value
                $Array_changwat_api =  $data_tumbon_api->CHANGWAT;
                $Array_ampur_api =  $data_tumbon_api->AMPUR;
            }
        }
        return $this->renderAjax('_Incident_Tumbon', [ //ตำบล/แขวง
            'Array_changwat_api' => $Array_changwat_api,
            'Array_ampur_api' => $Array_ampur_api,
            'Array_tumbon_api' => $Array_tumbon_api,
            'Check_Status' => "1",
            'DONATE_ID' => Yii::$app->request->post('DONATE_ID'),
            'TAMBON' => "",
        ]);
    }

    public function actionCheckzipcode(){ //ค้นหา ตำบล
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        //================================================== 1. API ตำบลทั้งหมด ZIPCODE  ============================================
        $API_tumbon_api = $this->API_province_area_api(Yii::$app->request->post('Tumbonstep3_ID'),Yii::$app->request->post('Ampurstep3_ID'),Yii::$app->request->post('Changwatstep3_ID'),$Access_Token['access_token']);
        if($API_tumbon_api->json_data[0]->ZIPCODE  == null){
            $Check_Status = "2";
        }else{
            $Check_Status = "1";
        }
        //============================================== สร้าง Array ตำบลทั้งหมด =======================================================
        $Array_zipcode_api = array();
        $DataID_Zipcode =  $API_tumbon_api->json_data[0]->ZIPCODE;
        $DataNAME_Zipcode =  $API_tumbon_api->json_data[0]->ZIPCODE;
        $Array_zipcode_api[$DataID_Zipcode]=$DataNAME_Zipcode; //การสร้าง Array แบบกำหนด  key value

        return $this->renderAjax('_Incident_Zipcode', [ //ตำบล/แขวง
            'Array_zipcode_api' => $Array_zipcode_api,
            'Check_Status' => $Check_Status,
            'DONATE_ID' => Yii::$app->request->post('DONATE_ID'),
        ]);
    }


    public function API_donate_update($DONATE_ID,$NAME_TYPE,$PNAME,$NAME,$LNAME,$TAXPAYER_NUMBER,
                                      $ADDRESS_NO,$ALLEY,$ROAD,$TAMBON,$DISTRICT,$PROVINCE,$ZIP_CODE,
                                      $TELEPHONE,$DONATE_NOTE,$LETTER_TYPE,$REGISTER_TYPE,$FIRSTSTAFF,$Token){
        $curl = curl_init();
        $Province_Group = 'DONATE_ID='.$DONATE_ID.'&NAME_TYPE='.$NAME_TYPE.'&PNAME='.$PNAME.'&NAME='.$NAME.'&LNAME='.$LNAME.'&TAXPAYER_NUMBER='.$TAXPAYER_NUMBER.
            '&ADDRESS_NO='.$ADDRESS_NO.'&ALLEY='.$ALLEY.'&ROAD='.$ROAD.'&TAMBON='.$TAMBON.'&DISTRICT='.$DISTRICT.'&PROVINCE='.$PROVINCE.'&ZIP_CODE='.$ZIP_CODE.
            '&TELEPHONE='.$TELEPHONE.'&DONATE_NOTE='.$DONATE_NOTE.'&LETTER_TYPE='.$LETTER_TYPE.'&REGISTER_TYPE='.$REGISTER_TYPE.'&FIRSTSTAFF='.$FIRSTSTAFF;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_update',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $Province_Group,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }


    public function API_donate_admin($ADMIN_ID,$ADMINTYPE_ID,$Token){
        $curl = curl_init();
        $ADMINToken = 'ADMIN_ID='.$ADMIN_ID.'&ADMINTYPE_ID='.$ADMINTYPE_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_admin',
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

    public function API_add_donate_admin($ADMIN_ID,$ADMINTYPE_ID,$ADMIN_IDCARD,$ADMIN_NAME,$ADMIN_AGENCY_ID,$TYPE_ID,$Token){
        $curl = curl_init();
        $ADMINToken = 'ADMIN_ID='.$ADMIN_ID.'&ADMINTYPE_ID='.$ADMINTYPE_ID.'&ADMIN_IDCARD='.$ADMIN_IDCARD.'&ADMIN_NAME='.$ADMIN_NAME.'&ADMIN_AGENCY_ID='.$ADMIN_AGENCY_ID.'&TYPE_ID='.$TYPE_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_admin',
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

    public function API_donate_home($SDATE,$EDATE,$Token){
        $curl = curl_init();
        $ADMINToken = 'SDATE='.$SDATE.'&EDATE='.$EDATE;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_home',
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

    public function API_donateitem_fpdf($DONATE_ID,$Token){
        $curl = curl_init();
        $ADMINToken = 'DONATE_ID='.$DONATE_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donateitem_fpdf',
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

    public function API_donateitemdt_fpdf($DONATE_ID,$Token){
        $curl = curl_init();
        $ADMINToken = 'DONATE_ID='.$DONATE_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donateitemdt_fpdf',
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

    public function API_staff_name($STAFF,$IDCRD,$Token){
        $curl = curl_init();
        $ADMINToken = 'STAFF='.$STAFF.'&IDCRD='.$IDCRD;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/allproject_api/staff_name',
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

    public function API_pname2_api($PNAME,$Token){
        $curl = curl_init();
        $ADMINToken = 'PNAME='.$PNAME;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/allproject_api/pname2_api',
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

    public function API_pname_api($Token){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/allproject_api/pname_api',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_province_area_api($TUMBON_ID,$AMPUR_ID,$CHANGWAT_ID,$Token){
        $curl = curl_init();
        $Province_Group = 'TUMBON_ID='.$TUMBON_ID.'&AMPUR_ID='.$AMPUR_ID.'&CHANGWAT_ID='.$CHANGWAT_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/allproject_api/province_area_api',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $Province_Group,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$Token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_CONFIRM_INSERT($DONATE_ID,$CON_DATE,$CON_TELEPHONE,$FIRSTSTAFF,$FIRSTDATE,$CONFIRMTYPE_ID,$Token){
        $curl = curl_init();
        $ADMINToken = 'DONATE_ID='.$DONATE_ID.'&CON_DATE='.$CON_DATE.'&CON_TELEPHONE='.$CON_TELEPHONE.'&FIRSTSTAFF='.$FIRSTSTAFF.
            '&FIRSTDATE='.$FIRSTDATE.'&CONFIRMTYPE_ID='.$CONFIRMTYPE_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_confirm',
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

    public function API_CONFIRM_UPDATE($DONATE_ID,$CON_TELEPHONE,$CONFIRMTYPE_ID,$Token){
        $curl = curl_init();
        $ADMINToken = 'DONATE_ID='.$DONATE_ID.'&CON_TELEPHONE='.$CON_TELEPHONE.'&CONFIRMTYPE_ID='.$CONFIRMTYPE_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_confirm',
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

    public function API_DONATE_CONFIRM_ID($DONATE_ID,$CONFIRMTYPE_ID,$Token){
        $curl = curl_init();
        $ADMINToken = 'DONATE_ID='.$DONATE_ID.'&CONFIRMTYPE_ID='.$CONFIRMTYPE_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_confirm',
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

}