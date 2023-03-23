<?php
namespace  RJDonate\controllers;

use RJDonate\models\AskDonationsForm;
use \yii\web\Controller;
use yii\helpers\Html;
use Yii;

class DonationController extends Controller
{

    public function actionIndex()
    {
        //=============== การใช้ session=======================
        //Yii::$app->session->get('arrayAccess_Token'); //session นำมาใช้
        //Yii::$app->session->set('arrayAccess_Token'); //session เก็บ
        //================================================

        $AskDonationsModel = new AskDonationsForm();

        //========================== วันที่และเวลาปัจจุบัน และ แปลงเป็นไทย =================================
        $Current_Date = date("Y/m/d"); //วันที่ปัจจุบัน
        $Current_Date2 = date("d/m/Y"); //วันที่ปัจจุบัน
        $Current_Time = date("H:i:s"); //เวลาที่ปัจจุบัน
        $Current_DateThai = Yii::$app->helper->dateThaiFull($Current_Date); // แปลงวันที่่ไทย
        //============ ตรวจสอบ session arrayAccess_Token กรณีมีการล้างค่า เป็น NULL  ===============
        if(Yii::$app->session->get('arrayAccess_Token') === NULL){
            Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
            return $this->redirect(['login/login_his']);
        }else{
            //===================== เรียกใช้ session arrayAccess_Token  ========================
            $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
//            var_dump($Access_Token['user']->staff);die();
//            var_dump($Current_DateThai,$Current_Time,$Access_Token['user']->staff_name,$Access_Token['user']->staff_div );die();
            if($Access_Token['admin']->json_data === []){
                $ADMIN_ID = "";
            }else{
                $ADMIN_ID = $Access_Token['admin']->json_data[0]->ADMIN_ID;
            }

            //============================================ 1. API ADMIN =========================================================================
            $API_donate_admin = $this->API_donate_admin($ADMIN_ID,"",$Access_Token['access_token']);
            //============================================ 2. API คำนำหน้าชื่อ pname_api ================================================================
            $API_pname_api = $this->API_pname_api($Access_Token['access_token']);

            //======== ตรวจสอบ Token หมดอายุหรือยัง $API_donate_admin->json_result ==============
            if($API_pname_api->json_result == true && $API_donate_admin->json_result == true){
                //============================================ 3. API จังหวัดทั้งหมด CHANGWAT_ID =========================================================
                $API_changwat_api = $this->API_province_area_api('','','CHANGWAT_ALL',$Access_Token['access_token']);
                //============================================ 4. API ค่า max รหัสใบรับบริจาค =================================================================
                $API_maxdonate_id = $this->API_maxdonate_id('MAXID',$Access_Token['access_token']);
                $Next_Booknum = $API_maxdonate_id->json_data[0]->MAXDONATE_ID + 1;
                //============================================ 5. API ITEMTYPE ทั้งหมด =================================================================
                $API_itemtype_id = $this->API_donate_item('ITEMTYPE_ALL','',$Access_Token['access_token']);
                //============================================ 6. API ITEM ทั้งหมด ======================================================================
                $API_item_id = $this->API_donate_item('','ITEM_ALL',$Access_Token['access_token']);
                //============================================ 6. APหน่งยนับ ทั้งหมด ======================================================================
                $API_cu_id = $this->API_donate_cu('CU_ALL',$Access_Token['access_token']);
                //============================================== สร้าง Array คำนำหน้าชื่อ =======================================================
                $Array_pname_api = array();
                if (isset($API_pname_api)) {
                    foreach ($API_pname_api->json_data as $data_pname_api){
                        $DataID_Pname =  $data_pname_api->PNAME;
                        $DataNAME_Pname =  $data_pname_api->NAME ;
                        $Array_pname_api[$DataID_Pname]=$DataNAME_Pname; //การสร้าง Array แบบกำหนด  key value
                    }
                }
                //============================================== สร้าง Array ตำบลทั้งหมด =======================================================
                $Array_tumbon_api = array();
                //============================================== สร้าง Array อำเภอทั้งหมด =======================================================
                $Array_ampur_api = array();
                //============================================== สร้าง Array จังหวัดทั้งหมด =======================================================
                $Array_changwat_api = array();
                if (isset($API_changwat_api)) {
                    foreach ($API_changwat_api->json_data as $data_changwat_api){
                        $DataID_Changwat =  $data_changwat_api->CHANGWAT;
                        $DataNAME_Changwat =  $data_changwat_api->CNAME ;
                        $Array_changwat_api[$DataID_Changwat]=$DataNAME_Changwat; //การสร้าง Array แบบกำหนด  key value
                    }
                }
                //============================================== สร้าง Array ITEMTYPE ทั้งหมด ============================================
                $Array_itemtype_api = array();
                if (isset($API_itemtype_id)) {
                    foreach ($API_itemtype_id->json_data as $data_itemtype_api){
                        $DataID_Itemtype =  $data_itemtype_api->ITEMTYPE_ID;
                        $DataNAME_Itemtype =  $data_itemtype_api->ITEM_NAME ;
                        $Array_itemtype_api[$DataID_Itemtype]=$DataNAME_Itemtype; //การสร้าง Array แบบกำหนด  key value
                    }
                }
                //============================================== สร้าง Array ITEM ทั้งหมด =================================================
                $Array_item_api = array();
                if (isset($API_item_id)) {
                    foreach ($API_item_id->json_data as $data_item_api){
                        $DataID_Item =  $data_item_api->ITEM_ID;
                        $DataNAME_Item =  $data_item_api->ITEM_NAME ;
                        $Array_item_api[$DataID_Item]=$DataNAME_Item; //การสร้าง Array แบบกำหนด  key value
                    }
                }
                //============================================== สร้าง Array หน่วยนับ ทั้งหมด =================================================
                $Array_cu_api = array();
                if (isset($API_cu_id)) {
                    foreach ($API_cu_id->json_data as $data_cu_api){
                        $DataID_Item =  $data_cu_api->CU_ID;
                        $DataNAME_Item =  $data_cu_api->CU_NAME ;
                        $Array_cu_api[$DataID_Item]=$DataNAME_Item; //การสร้าง Array แบบกำหนด  key value
                    }
                }
                //=============================================== ข้อมูลหลังจากกดปุ่มบันทึก =========================================================
                if(Yii::$app->request->post()) {
                    if(Yii::$app->request->post('Check-button') == 1){
//                        var_dump(Yii::$app->request->post());die();
//                        var_dump(Yii::$app->request->post('Num_Stack'));die();
//                        var_dump(Yii::$app->request->post('Itemtype_On1'));die();

                        if (isset($Next_Booknum)) {
                            //บันทึกข้อมุลที่อยู่
                            $this->API_donate_insert($Next_Booknum , Yii::$app->request->post('AskDonationsForm')["Name_Type"] ,Yii::$app->request->post('AskDonationsForm')["My_Pname"],
                                Yii::$app->request->post('AskDonationsForm')["My_Name"],Yii::$app->request->post('AskDonationsForm')["My_Lname"],Yii::$app->request->post('AskDonationsForm')["Taxpayer_Number"],
                                $Current_Date2." ".$Current_Time,Yii::$app->request->post('AskDonationsForm')["Address_No"],Yii::$app->request->post('AskDonationsForm')["Alley"],Yii::$app->request->post('AskDonationsForm')["Road"],
                                Yii::$app->request->post('Tambon'),Yii::$app->request->post('Ampur'),Yii::$app->request->post('Changwat'),Yii::$app->request->post('Zipcode'),
                                Yii::$app->request->post('AskDonationsForm')["Telephone"],Yii::$app->request->post('AskDonationsForm')["Donate_Note"],Yii::$app->request->post('AskDonationsForm')["Letter_Type"],
                                Yii::$app->request->post('AskDonationsForm')["Register_Type"],'',$Access_Token['user']->staff,'',$Access_Token['access_token']);
                            //บันทึกข้อมุล ITEM
                            for ($x = 1; $x <= Yii::$app->request->post('Num_Stack'); $x++) {
                                $this->API_item_insert(Yii::$app->request->post('Itemmaster_On'.$x),Yii::$app->request->post('Itemtype_On'.$x),
                                    $Next_Booknum,$Current_Date2." ".$Current_Time,Yii::$app->request->post('Quantity_On'.$x),Yii::$app->request->post('Itemcu_On'.$x),
                                    Yii::$app->request->post('Price_On'.$x),Yii::$app->request->post('Othername_On'.$x),$x,$Access_Token['access_token']);
                            }
                            Yii::$app->session->setFlash('success', '<b>บันทึกข้อมูลสำเร็จ</b>');
                        }
                    }else{
                        var_dump(Yii::$app->request->post());die();
                    }
                }
            }else{
                Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
                return $this->redirect(['login/login_his']);
            }
        }

        return $this->render('index',[
            'model' => $AskDonationsModel,
            'Access_Token' => $Access_Token,
            'Current_DateThai' => $Current_DateThai,
            'Current_Time' => $Current_Time,
            'Array_pname_api' => $Array_pname_api,
            'Array_tumbon_api' => $Array_tumbon_api,
            'Array_ampur_api' => $Array_ampur_api,
            'Array_changwat_api' => $Array_changwat_api,
            'Next_Booknum' => $Next_Booknum,//เลขหนังสือ
            'Array_itemtype_api' => $Array_itemtype_api,
            'Array_item_api' => $Array_item_api,
            'Array_cu_api' => $Array_cu_api,
        ]);
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
            'Length_V2' => Yii::$app->request->post('Length_V2')
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
            'Length_V2' => Yii::$app->request->post('Length_V2')
        ]);
    }

    public function actionCheckzipcode(){ //ค้นหา ตำบล
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        //================================================== 1. API ตำบลทั้งหมด ZIPCODE  ============================================
        $API_tumbon_api = $this->API_province_area_api(Yii::$app->request->post('Tumbonstep3_ID'),Yii::$app->request->post('Ampurstep3_ID'),Yii::$app->request->post('Changwatstep3_ID'),$Access_Token['access_token']);
        //============================================== สร้าง Array ตำบลทั้งหมด =======================================================
        if($API_tumbon_api->json_data[0]->ZIPCODE  == null){
            $Check_Status = "2";
        }else{
            $Check_Status = "1";
        }
        $Array_zipcode_api = array();
        $DataID_Zipcode =  $API_tumbon_api->json_data[0]->ZIPCODE;
        $DataNAME_Zipcode =  $API_tumbon_api->json_data[0]->ZIPCODE;
        $Array_zipcode_api[$DataID_Zipcode]=$DataNAME_Zipcode; //การสร้าง Array แบบกำหนด  key value

        return $this->renderAjax('_Incident_Zipcode', [ //ตำบล/แขวง
            'Array_zipcode_api' => $Array_zipcode_api,
            'Check_Status' => $Check_Status,
            'Length_V2' => Yii::$app->request->post('Length_V2')
        ]);
    }



    public function actionCheckitemall(){ //ค้นหา ของบริจาค
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        //var_dump(Yii::$app->request->post());
        if(Yii::$app->request->post('Item_ID') == ""){
            $Value_itemtype_api = "";
            //============================================ 1. API ITEMTYPE ทั้งหมด =================================================================
            $API_itemtype_id = $this->API_donate_item('ITEMTYPE_ALL','',$Access_Token['access_token']);
            //============================================== สร้าง Array ITEMTYPE ทั้งหมด ============================================
            $Array_itemtype_api = array();
            if (isset($API_itemtype_id)) {
                foreach ($API_itemtype_id->json_data as $data_itemtype_api){
                    $DataID_Itemtype =  $data_itemtype_api->ITEMTYPE_ID;
                    $DataNAME_Itemtype =  $data_itemtype_api->ITEM_NAME ;
                    $Array_itemtype_api[$DataID_Itemtype]=$DataNAME_Itemtype; //การสร้าง Array แบบกำหนด  key value
                }
            }
        }else{
            //============================================ 1. API ITEMTYPE ทั้งหมด =========================================================================
            $API_itemtype_id = $this->API_donate_item('ITEMTYPE_ALL','',$Access_Token['access_token']);
            //============================================ 2. API ITEMTYPE ของ ITEM ======================================================================
            $API_value_itemtype_id = $this->API_donate_item('',Yii::$app->request->post('Item_ID'),$Access_Token['access_token']);
            $Value_itemtype_api =  $API_value_itemtype_id->json_data[0]->ITEMTYPE_ID;
            //============================================== สร้าง Array ITEMTYPE ทั้งหมด ============================================
            $Array_itemtype_api = array();
            if (isset($API_itemtype_id)) {
                foreach ($API_itemtype_id->json_data as $data_itemtype_api){
                    $DataID_Itemtype =  $data_itemtype_api->ITEMTYPE_ID;
                    $DataNAME_Itemtype =  $data_itemtype_api->ITEM_NAME ;
                    $Array_itemtype_api[$DataID_Itemtype]=$DataNAME_Itemtype; //การสร้าง Array แบบกำหนด  key value
                }
            }
        }

        $Value_item_api = Yii::$app->request->post('Item_ID');
        //============================================ 1. API ITEM ทั้งหมด ======================================================================
        $API_item_id = $this->API_donate_item('','ITEM_ALL',$Access_Token['access_token']);
        //============================================ 2. APหน่งยนับ ทั้งหมด ========================================================================
        $API_cu_id = $this->API_donate_cu('CU_ALL',$Access_Token['access_token']);
        //============================================== สร้าง Array ITEM ทั้งหมด =================================================
        $Array_item_api = array();
        if (isset($API_item_id)) {
            foreach ($API_item_id->json_data as $data_item_api){
                $DataID_Item =  $data_item_api->ITEM_ID;
                $DataNAME_Item =  $data_item_api->ITEM_NAME ;
                $Array_item_api[$DataID_Item]=$DataNAME_Item; //การสร้าง Array แบบกำหนด  key value
            }
        }
        //============================================== สร้าง Array หน่วยนับ ทั้งหมด =================================================
        $Array_cu_api = array();
        if (isset($API_cu_id)) {
            foreach ($API_cu_id->json_data as $data_cu_api){
                $DataID_Item =  $data_cu_api->CU_ID;
                $DataNAME_Item =  $data_cu_api->CU_NAME ;
                $Array_cu_api[$DataID_Item]=$DataNAME_Item; //การสร้าง Array แบบกำหนด  key value
            }
        }

        return $this->renderAjax('_Incident_donatItem', [ //ประเภทของบริจาค
            'Array_cu_api' => $Array_cu_api,
            'Array_itemtype_api' => $Array_itemtype_api,
            'Array_item_api' => $Array_item_api,
            'Value_itemtype_api' => $Value_itemtype_api,
            'Value_item_api' => $Value_item_api,
            'Check_Status'=>"1",
            'Length_V2' => Yii::$app->request->post('Length_V2'),
            'Title_Length' => Yii::$app->request->post('Title_Length'),
            'FontS_rm' => Yii::$app->request->post('FontS_rm'),
            'Num_Stack' => Yii::$app->request->post('Num_Stack')
        ]);
    }

    public function actionCheckitemtype(){ //ค้นหา ของบริจาค
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        //var_dump(Yii::$app->request->post());
        if(Yii::$app->request->post('Item_ID') == ""){
            $Value_itemtype_api = "";
            //============================================ 1. API ITEMTYPE ทั้งหมด =================================================================
            $API_itemtype_id = $this->API_donate_item('ITEMTYPE_ALL','',$Access_Token['access_token']);
            //============================================== สร้าง Array ITEMTYPE ทั้งหมด ============================================
            $Array_itemtype_api = array();
            if (isset($API_itemtype_id)) {
                foreach ($API_itemtype_id->json_data as $data_itemtype_api){
                    $DataID_Itemtype =  $data_itemtype_api->ITEMTYPE_ID;
                    $DataNAME_Itemtype =  $data_itemtype_api->ITEM_NAME ;
                    $Array_itemtype_api[$DataID_Itemtype]=$DataNAME_Itemtype; //การสร้าง Array แบบกำหนด  key value
                }
            }
        }else{
            //============================================ 1. API ITEMTYPE ทั้งหมด =========================================================================
            $API_itemtype_id = $this->API_donate_item('ITEMTYPE_ALL','',$Access_Token['access_token']);
            //============================================ 2. API ITEMTYPE ของ ITEM ======================================================================
            $API_value_itemtype_id = $this->API_donate_item('',Yii::$app->request->post('Item_ID'),$Access_Token['access_token']);
            $Value_itemtype_api =  $API_value_itemtype_id->json_data[0]->ITEMTYPE_ID;
            //============================================== สร้าง Array ITEMTYPE ทั้งหมด ============================================
            $Array_itemtype_api = array();
            if (isset($API_itemtype_id)) {
                foreach ($API_itemtype_id->json_data as $data_itemtype_api){
                    $DataID_Itemtype =  $data_itemtype_api->ITEMTYPE_ID;
                    $DataNAME_Itemtype =  $data_itemtype_api->ITEM_NAME ;
                    $Array_itemtype_api[$DataID_Itemtype]=$DataNAME_Itemtype; //การสร้าง Array แบบกำหนด  key value
                }
            }
        }

        return $this->renderAjax('_Incident_Itemtype', [ //ประเภทของบริจาค
            'Array_itemtype_api' => $Array_itemtype_api,
            'Value_itemtype_api' => $Value_itemtype_api,
            'Check_Status'=>"1",
            'Length_V2' => Yii::$app->request->post('Length_V2'),
            'Num_Stack' => Yii::$app->request->post('Num_Stack')
        ]);
    }

    public function actionCheckitem(){ //ค้นหา ประเภทของบริจาค
        $Access_Token =  Yii::$app->session->get('arrayAccess_Token');
        //var_dump(Yii::$app->request->post());die();
        if(Yii::$app->request->post('Itemtype_ID') == ""){
            //============================================ 1. API ITEM ทั้งหมด ======================================================================
            $API_item_id = $this->API_donate_item('','ITEM_ALL',$Access_Token['access_token']);
            //============================================== สร้าง Array ITEM ทั้งหมด =================================================
            $Array_item_api = array();
            if (isset($API_item_id)) {
                foreach ($API_item_id->json_data as $data_item_api){
                    $DataID_Item =  $data_item_api->ITEM_ID;
                    $DataNAME_Item =  $data_item_api->ITEM_NAME ;
                    $Array_item_api[$DataID_Item]=$DataNAME_Item; //การสร้าง Array แบบกำหนด  key value
                }
            }
        }else{
            //============================================ 1. API ITEM ของ ITEMTYPE ======================================================================
            $API_item_id = $this->API_donate_item(Yii::$app->request->post('Itemtype_ID'),'',$Access_Token['access_token']);
            //============================================== สร้าง Array ITEM ทั้งหมด ==========================================================================
            $Array_item_api = array();
            if (isset($API_item_id)) {
                foreach ($API_item_id->json_data as $data_item_api){
                    $DataID_Item =  $data_item_api->ITEM_ID;
                    $DataNAME_Item =  $data_item_api->ITEM_NAME ;
                    $Array_item_api[$DataID_Item]=$DataNAME_Item; //การสร้าง Array แบบกำหนด  key value
                }
            }
        }

        return $this->renderAjax('_Incident_Item', [ //ของบริจาค
            'Array_item_api' => $Array_item_api,
            'Value_item_api' => "",
            'Check_Status'=>"1",
            'Length_V2' => Yii::$app->request->post('Length_V2'),
            'Num_Stack' => Yii::$app->request->post('Num_Stack')
        ]);
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

    public function API_donate_item($ITEMTYPE_ID,$ITEM_ID,$Token){
        $curl = curl_init();
        $Province_Group = 'ITEMTYPE_ID='.$ITEMTYPE_ID.'&ITEM_ID='.$ITEM_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_item',
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

    public function API_donate_cu($CU_ID,$Token){
        $curl = curl_init();
        $Province_Group = 'CU_ID='.$CU_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_cu',
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

    public function API_maxdonate_id($MAXDONATE_ID,$Token){
        $curl = curl_init();
        $Province_Group = 'MAXDONATE_ID='.$MAXDONATE_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/maxdonate_id',
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

    public function API_donate_insert($DONATE_ID,$NAME_TYPE,$PNAME,$NAME,$LNAME,$TAXPAYER_NUMBER,$DONATE_DATE,
                                      $ADDRESS_NO,$ALLEY,$ROAD,$TAMBON,$DISTRICT,$PROVINCE,$ZIP_CODE,
                                      $TELEPHONE,$DONATE_NOTE,$LETTER_TYPE,$REGISTER_TYPE,$CANCELDATE,$FIRSTSTAFF,$CANCELSTAFF,$Token){
        $curl = curl_init();
        $Province_Group = 'DONATE_ID='.$DONATE_ID.'&NAME_TYPE='.$NAME_TYPE.'&PNAME='.$PNAME.'&NAME='.$NAME.'&LNAME='.$LNAME.'&TAXPAYER_NUMBER='.$TAXPAYER_NUMBER.'&DONATE_DATE='.$DONATE_DATE.
            '&ADDRESS_NO='.$ADDRESS_NO.'&ALLEY='.$ALLEY.'&ROAD='.$ROAD.'&TAMBON='.$TAMBON.'&DISTRICT='.$DISTRICT.'&PROVINCE='.$PROVINCE.'&ZIP_CODE='.$ZIP_CODE.
            '&TELEPHONE='.$TELEPHONE.'&DONATE_NOTE='.$DONATE_NOTE.'&LETTER_TYPE='.$LETTER_TYPE.'&REGISTER_TYPE='.$REGISTER_TYPE.'&CANCELDATE='.$CANCELDATE.'&FIRSTSTAFF='.$FIRSTSTAFF.'&CANCELSTAFF='.$CANCELSTAFF;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/donate_insert',
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

    public function API_item_insert($ITEM_ID,$ITEMTYPE_ID,$DONATE_ID,$DONATE_DATE,$QUANTITY,$COUNTING_UNIT,$PRICE,$ITEM_NAME,$ORDER_NUM,$Token){
        $curl = curl_init();
        $Province_Group = 'ITEM_ID='.$ITEM_ID.'&ITEMTYPE_ID='.$ITEMTYPE_ID.'&DONATE_ID='.$DONATE_ID.'&DONATE_DATE='.$DONATE_DATE.'&QUANTITY='.$QUANTITY.
            '&COUNTING_UNIT='.$COUNTING_UNIT.'&PRICE='.$PRICE.'&ITEM_NAME='.$ITEM_NAME.'&ORDER_NUM='.$ORDER_NUM;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/donate_api/item_insert',
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


}