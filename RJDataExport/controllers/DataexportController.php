<?php
namespace  RJDataExport\controllers;


use \yii\web\Controller;
use RJDataExport\models\StatuserForm;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yii;


class DataexportController extends Controller
{

    public function actionOds_index()
    {

        //=============== การใช้ session=================
        //Yii::$app->session->get('access_token'); //session นำมาใช้
        //Yii::$app->session->set('access_token'); //session เก็บ
        //==========================================

        $StatuserModel = new StatuserForm();
        $API = null;
        $SDateThai = null;
        $EDateThai = null;


        //======================= API ESI Check Token==============
        $API = $this->API_ods_uc_permissionsV2("", "", "0");
        $API_pttype = $this->API_check_pttype_name('303011100','302030001');
        //=======================================================

        if ($API->json_result == true) {
            //=========================================== สร้าง Array ข้อมูลสิทธิ์ ======================================================
            $Array_pttype = array();
            $Array_pttype["0"]="Search All";
            foreach ($API_pttype->json_data as $data_pttype){
                $DataID_Pttype =  $data_pttype->PTTYPE1;
                $DataNAME_Pttype =  $data_pttype->PTTYPE_NM ;
                $Array_pttype[$DataID_Pttype]=$DataNAME_Pttype; //การสร้าง Array แบบกำหนด  key value
            }
//            var_dump($Array_pttype);die();
            //=================================================================================================================
            if (Yii::$app->request->post()) {
                $StatuserModel->load(Yii::$app->request->post());
                $SelectedDate = $StatuserModel->SelectedDate;
                if ($SelectedDate == "") { // Check วันที่ ส่งมา
                    Yii::$app->session->setFlash('error', 'กรุณาระบุวันที่');
                } else {
                    $SelectedDateCut = explode(" - ", $SelectedDate);
                    $dateS = $SelectedDateCut[0];
                    $dateE = $SelectedDateCut[1];

                    $datecutS = explode("/", $dateS);
                    $datecutE = explode("/", $dateE);
                    $dateSV2 = $datecutS[2] . "-" . $datecutS[1] . "-" . $datecutS[0];
                    $dateEV2 = $datecutE[2] . "-" . $datecutE[1] . "-" . $datecutE[0];

                    //==================== แปลงวันที่่ไทย ====================
                    $SDateThai = Yii::$app->helper->dateThaiFull($dateSV2); // แปลงวันที่่ไทย
                    $EDateThai = Yii::$app->helper->dateThaiFull($dateEV2); // แปลงวันที่่ไทย
                    //=================================================

                    //================= หาจำนวนวันที่ ไม่เกิน 30 วัน กรณีคนละเดือน คนละปี ===============
                    $SDayEnd = date("Y-m-t", strtotime($dateSV2)); //หาวันสุดท้ายของเดือนที่เลือก
                    $SDayEndCut = explode("-", $SDayEnd);
                    if ($datecutS[2] == $datecutE[2] && $datecutS[1] == $datecutE[1]) {
                        $SUMDayCheck = "0";
                    } else {
                        $SUMDayCheck = ($SDayEndCut[2] - $datecutS[0]) + 1 + $datecutE[0];
                    }
                    //================================================================
                    if (Yii::$app->request->post('Check-button') == 1) {
                        if(Yii::$app->request->post('Pttype_ID') == null){
                            $Pttype_ID = "0";
                        }else{
                            $Pttype_ID = Yii::$app->request->post('Pttype_ID');
                        }

                        if ($datecutS[2] == $datecutE[2] && $datecutS[1] == $datecutE[1] || $SUMDayCheck <= 30) { // Check ว่าเป็น เดือนเดียว ปีเดียว กันไหม
                            //========================== API ods_uc_permissions =========================
                            $API = $this->API_ods_uc_permissionsV2($dateS, $dateE,$Pttype_ID);
//                            var_dump(Yii::$app->request->post('Pttype_ID'));die();
                            //=========================================================================
                        } else {
                            $API = null;
                            Yii::$app->session->setFlash('error', 'ไม่สามารถตรวจสอบข้อมูลได้ <b><u>วันที่เลือกย้อนหลังต้องไม่เกิน 30 วัน</u></b> กรุณาระบุวันที่ใหม่อีกครั้ง');
                        }
                    } else {
                        if(Yii::$app->request->post('Pttype_ID') == null){
                            $Pttype_ID = "0";
                        }else{
                            $Pttype_ID = Yii::$app->request->post('Pttype_ID');
                        }

                        if ($datecutS[2] == $datecutE[2] && $datecutS[1] == $datecutE[1] || $SUMDayCheck <= 30) { // Check ว่าเป็น เดือนเดียว ปีเดียว กันไหม
                            //========================== API ods_uc_permissions =========================
                            $API = $this->API_ods_uc_permissionsV2($dateS, $dateE,$Pttype_ID);
                            //=========================================================================
                        } else {
                            $API = null;
                            Yii::$app->session->setFlash('error', 'ไม่สามารถตรวจสอบข้อมูลได้ <b><u>วันที่เลือกย้อนหลังต้องไม่เกิน 30 วัน</u></b> กรุณาระบุวันที่ใหม่อีกครั้ง');
                        }
//                        $this->Text_16_Files();
                    }
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
            return $this->redirect(['login/login_his']);
        }

        return $this->render('ods_index', [
            'model' => $StatuserModel,
            'SDateThai' => $SDateThai,
            'EDateThai' => $EDateThai,
            'API' => $API,
            'Array_pttype' => $Array_pttype
        ]);
    }

    public function actionData16_files()
    {
        var_dump(Yii::$app->request->post());
        return $this->render('data16_files');
    }

    public function API_ods_uc_permissions($SDATE, $EDATE, $PTTYPE, $HNID)
    {
        $curl = curl_init();
        $DataToken = 'SDATE=' . $SDATE . '&EDATE=' . $EDATE . '&PTTYPE=' . $PTTYPE . '&HNID=' . $HNID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjdataexport_api/ods_uc_permissions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . Yii::$app->session->get('access_token'),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_ods_uc_permissionsV2($SDATE, $EDATE, $PTTYPE)
    {
        $curl = curl_init();
        $DataToken = 'SDATE=' . $SDATE . '&EDATE=' . $EDATE . '&PTTYPE=' . $PTTYPE;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjdataexport_api/ods_uc_permissionsV2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . Yii::$app->session->get('access_token'),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_check_ods_uc90($HNDATE, $VNDATE)
    {
        $curl = curl_init();
        $DataToken = 'HNDATE=' . $HNDATE . '&VNDATE=' . $VNDATE;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjdataexport_api/check_ods_uc90',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . Yii::$app->session->get('access_token'),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_check_service_charge($HNDATE, $VNDATE, $CLINICLCT, $PTTYPE)
    {
        $curl = curl_init();
        $DataToken = 'HNDATE=' . $HNDATE . '&VNDATE=' . $VNDATE . '&CLINICLCT=' . $CLINICLCT . '&PTTYPE=' . $PTTYPE;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjdataexport_api/check_service_charge',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . Yii::$app->session->get('access_token'),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_check_pttype_name($CHECK1,$CHECK2)
    {
        $curl = curl_init();
        $DataToken = 'CHECK1=' . $CHECK1 . '&CHECK2=' . $CHECK2 ;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjdataexport_api/check_pttype_name',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . Yii::$app->session->get('access_token'),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_icd10icd9_one($HNID, $VNDATE, $CLINICLCTID)
    {
        $curl = curl_init();
        $DataToken = 'HNID=' . $HNID . '&VNDATE=' . $VNDATE . '&CLINICLCTID=' . $CLINICLCTID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjdataexport_api/icd10icd9_one',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . Yii::$app->session->get('access_token'),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_icd10icd9_all($HNID, $VNDATE)
    {
        $curl = curl_init();
        $DataToken = 'HNID=' . $HNID . '&VNDATE=' . $VNDATE;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjdataexport_api/icd10icd9_all',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . Yii::$app->session->get('access_token'),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }


    public function Text_16_Files()
    {

        // ========================================================= เขียนข้อมูลลงไฟล์ text (.txt) ด้วย PHP ==================================================================
        // http://code.function.in.th/php/file
        // mode
        // fopen(filename,mode,include_path,context)
        // “r” (อ่านอย่างเดียว. เริ่มต้นที่จุดเริ่มต้นของไฟล์)
        // “r+” (อ่าน / เขียน. เริ่มต้นที่จุดเริ่มต้นของไฟล์)
        // “w” (เขียนอย่างเดียว เปิดขึ้นมาแล้วล้างเนื้อหาเก่าทิ้ง หรือสร้างไฟล์ใหม่ถ้าไม่มีไฟล์)
        // “w+” (อ่าน / เขียน เปิดขึ้นมาแล้วล้างเนื้อหาเก่าทิ้ง หรือสร้างไฟล์ใหม่ถ้าไม่มีไฟล์)
        // “a” (เขียนเท่านั้น. เปิดขึ้นมาแล้วเขียนต่อท้ายข้อมูลในไฟล์ หรือสร้างไฟล์ใหม่ถ้าไม่มีไฟล์)
        // “a+” (อ่าน / เขียน. เปิดขึ้นมาแล้วเขียนต่อท้ายข้อมูลในไฟล์ หรือสร้างไฟล์ใหม่ถ้าไม่มีไฟล์)
        // “x” (เขียนเท่านั้น. สร้างไฟล์ใหม่. ถ้ามีไฟล์อยู่แล้วจะ Returns FALSE)
        // “x+” (อ่าน / เขียน. สร้างไฟล์ใหม่. ถ้ามีไฟล์อยู่แล้วจะ Returns FALSE)

        //======================================================= 01 INS มาตรฐานแฟ้มข้อมูลผู้มีสิทธิการรักษาพยาบาล ******
        $Files_INS = 'Text16Files/INS.txt'; //ชื่อไฟล์
        $objFopen_INS = fopen($Files_INS, 'w') or die("Unable to open file!");
        $str_INS = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n01 INS";
        fwrite($objFopen_INS, $str_INS);
        fclose($objFopen_INS);

        //======================================================= 02 PAT มาตรฐานแฟ้มข้อมูลผู้ป่วยกลาง ******
        $Files_PAT = 'Text16Files/PAT.txt'; //ชื่อไฟล์
        $objFopen_PAT = fopen($Files_PAT, 'w') or die("Unable to open file!");
        $str_PAT = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n02 PAT";
        fwrite($objFopen_PAT, $str_PAT);
        fclose($objFopen_PAT);

        //======================================================= 03 OPD มาตรฐานแฟ้มข้อมูลการมารับบริการผู้ป่วยนอก
        $Files_OPD = 'Text16Files/OPD.txt'; //ชื่อไฟล์
        $objFopen_OPD = fopen($Files_OPD, 'w') or die("Unable to open file!");
        $str_OPD = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n03 OPD";
        fwrite($objFopen_OPD, $str_OPD);
        fclose($objFopen_OPD);

        //======================================================= 04 ORF มาตรฐานแฟ้มข้อมูลผู้ป่วยนอกที่ต้องส่งต่อ
        $Files_ORF = 'Text16Files/ORF.txt'; //ชื่อไฟล์
        $objFopen_ORF = fopen($Files_ORF, 'w') or die("Unable to open file!");
        $str_ORF = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n04 ORF";
        fwrite($objFopen_ORF, $str_ORF);
        fclose($objFopen_ORF);

        //======================================================= 05 ODX มาตรฐานแฟ้มข้อมูลวินิจฉัยโรคผู้ป่วยนอก
        $Files_ODX = 'Text16Files/ODX.txt'; //ชื่อไฟล์
        $objFopen_ODX = fopen($Files_ODX, 'w') or die("Unable to open file!");
        $str_ODX = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n05 ODX";
        fwrite($objFopen_ODX, $str_ODX);
        fclose($objFopen_ODX);

        //======================================================= 06 OOP มาตรฐานแฟ้มข้อมูลหัตถการผู้ป่วยนอก
        $Files_OOP = 'Text16Files/OOP.txt'; //ชื่อไฟล์
        $objFopen_OOP = fopen($Files_OOP, 'w') or die("Unable to open file!");
        $str_OOP = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n06 OOP";
        fwrite($objFopen_OOP, $str_OOP);
        fclose($objFopen_OOP);

        //======================================================= 07 IPD มาตรฐานแฟ้มข้อมูลผู้ป่วยใน ******
        $Files_IPD = 'Text16Files/IPD.txt'; //ชื่อไฟล์
        $objFopen_IPD = fopen($Files_IPD, 'w') or die("Unable to open file!");
        $str_IPD = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n07 IPD";
        fwrite($objFopen_IPD, $str_IPD);
        fclose($objFopen_IPD);

        //======================================================= 08 IRF มาตรฐานแฟ้มข้อมูลผู้ป่วยในที่ต้องส่งต่อ
        $Files_IRF = 'Text16Files/IRF.txt'; //ชื่อไฟล์
        $objFopen_IRF = fopen($Files_IRF, 'w') or die("Unable to open file!");
        $str_IRF = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n08 IRF";
        fwrite($objFopen_IRF, $str_IRF);
        fclose($objFopen_IRF);

        //======================================================= 09 IDX มาตรฐานแฟ้มข้อมูลวินิจฉัยโรคผู้ป่วยใน ******
        $Files_IDX = 'Text16Files/IDX.txt'; //ชื่อไฟล์
        $objFopen_IDX = fopen($Files_IDX, 'w') or die("Unable to open file!");
        $str_IDX = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n09 IDX";
        fwrite($objFopen_IDX, $str_IDX);
        fclose($objFopen_IDX);

        //======================================================= 10 IOP มาตรฐานแฟ้มข้อมูลหัตถการผู้ป่วยใน ******
        $Files_IOP = 'Text16Files/IOP.txt'; //ชื่อไฟล์
        $objFopen_IOP = fopen($Files_IOP, 'w') or die("Unable to open file!");
        $str_IOP = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n10 IOP";
        fwrite($objFopen_IOP, $str_IOP);
        fclose($objFopen_IOP);

        //======================================================= 11 CHT มาตรฐานแฟ้มข้อมูลการเงิน (แบบสรุป) ******
        $Files_CHT = 'Text16Files/CHT.txt'; //ชื่อไฟล์
        $objFopen_CHT = fopen($Files_CHT, 'w') or die("Unable to open file!");
        $str_CHT = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n11 CHT";
        fwrite($objFopen_CHT, $str_CHT);
        fclose($objFopen_CHT);

        //======================================================= 12 CHA มาตรฐานแฟ้มข้อมูลการเงิน (แบบรายละเอียด) ******
        $Files_CHA = 'Text16Files/CHA.txt'; //ชื่อไฟล์
        $objFopen_CHA = fopen($Files_CHA, 'w') or die("Unable to open file!");
        $str_CHA = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n12 CHA";
        fwrite($objFopen_CHA, $str_CHA);
        fclose($objFopen_CHA);

        //======================================================= 13 AER มาตรฐานแฟ้มข้อมูลอุบัติเหตุ ฉุกเฉิน และรับส่งเพื่อรักษา
        $Files_AER = 'Text16Files/AER.txt'; //ชื่อไฟล์
        $objFopen_AER = fopen($Files_AER, 'w') or die("Unable to open file!");
        $str_AER = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n13 AER";
        fwrite($objFopen_AER, $str_AER);
        fclose($objFopen_AER);

        //======================================================= 14 ADP มาตรฐานแฟ้มข้อมูลค่าใช้จ่ายเพิ่ม และบริการที่ยังไม่ได้จัดหมวด ******
        $Files_ADP = 'Text16Files/ADP.txt'; //ชื่อไฟล์
        $objFopen_ADP = fopen($Files_ADP, 'w') or die("Unable to open file!");
        $str_ADP = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n14 ADP";
        fwrite($objFopen_ADP, $str_ADP);
        fclose($objFopen_ADP);

        //======================================================= 15 LVD มาตรฐานแฟ้มข้อมูลกรณีที่ผู้ป่วยมีการลากลับบ้าน (Leave day)
        $Files_LVD = 'Text16Files/LVD.txt'; //ชื่อไฟล์
        $objFopen_LVD = fopen($Files_LVD, 'w') or die("Unable to open file!");
        $str_LVD = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n15 LVD";
        fwrite($objFopen_LVD, $str_LVD);
        fclose($objFopen_LVD);

        //======================================================= 16 DRU มาตรฐานแฟ้มข้อมูลการใช้ยา ******
        $Files_DRU = 'Text16Files/DRU.txt'; //ชื่อไฟล์
        $objFopen_DRU = fopen($Files_DRU, 'w') or die("Unable to open file!");
        $str_DRU = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n16 DRU";
        fwrite($objFopen_DRU, $str_DRU);
        fclose($objFopen_DRU);

        //======================================================= 17 LABFU แฟ้มข้อมูลการตรวจทางห้องปฎิบัติการของผู้ป่วยโรคเรื้อรัง
        $Files_LABFU = 'Text16Files/LABFU.txt'; //ชื่อไฟล์
        $objFopen_LABFU = fopen($Files_LABFU, 'w') or die("Unable to open file!");
        $str_LABFU = "\r\nทดสอบเขียนข้อมูลลงในไฟล์ \r\nต่อจากข้อมูลเดิม\r\n17 LABFU";
        fwrite($objFopen_LABFU, $str_LABFU);
        fclose($objFopen_LABFU);


        //unlink($file); // ใช้ลบไฟล์
        //===================================================================== สร้าง zip =========================================================================
        $zip = new \ZipArchive();
        $filename = "Text16Files/ECLAIM_16_FILES.zip";
        if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
            exit("cannot open <$filename>\n");
        }
        $zip->addFile('Text16Files/people1.txt');
        $zip->addFile('Text16Files/people2.txt');
        $zip->close();
        //===================================================================== Download zip ====================================================================
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename('Text16Files/ECLAIM_16_FILES.zip'));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize('Text16Files/ECLAIM_16_FILES.zip'));
        ob_clean();
        flush();
        readfile('Text16Files/ECLAIM_16_FILES.zip');
        exit;
        //======================================================================================================================================================
    }

    public function actionFpdfreport()
    {
        date_default_timezone_set('Asia/Bangkok');
        require(dirname(__FILE__) . '/../../web/assets/vendor_add_on/FPDF/fpdf.php');

        //==================== วัน/เดือน/ปี ปัจจุบัน ========================
        $ToDay = date('d/m/Y');
        $ToDayCut = explode("/", $ToDay);
        $YearTH = $ToDayCut[2] + 543;
        $ToDayTH = $ToDayCut[0] . "/" . $ToDayCut[1] . "/" . $YearTH;
        //=========================================================


        //====================== ค่าตัวแปล User ========================
        $User = Yii::$app->request->post("User");
        $HN_Set1 = substr($User["HN"], 2);
        $HN_Set2 = substr($User["HN"], 0, 2);
        $HN_Set3 = $HN_Set1 . "-" . $HN_Set2;
        //=========================================================


        //=========================== TDATE  =========================
        $TDATE = explode(" ", $User["DDATE"]);
        if ($TDATE[1] == "ม.ค.") {
            $TMONTH = "01";
        } else if ($TDATE[1] == "ก.พ.") {
            $TMONTH = "02";
        } else if ($TDATE[1] == "มี.ค.") {
            $TMONTH = "03";
        } else if ($TDATE[1] == "เม.ย.") {
            $TMONTH = "04";
        } else if ($TDATE[1] == "พ.ค.") {
            $TMONTH = "05";
        } else if ($TDATE[1] == "มิ.ย.") {
            $TMONTH = "06";
        } else if ($TDATE[1] == "ก.ค.") {
            $TMONTH = "07";
        } else if ($TDATE[1] == "ส.ค.") {
            $TMONTH = "08";
        } else if ($TDATE[1] == "ก.ย.") {
            $TMONTH = "09";
        } else if ($TDATE[1] == "ต.ค.") {
            $TMONTH = "10";
        } else if ($TDATE[1] == "พ.ย.") {
            $TMONTH = "11";
        } else if ($TDATE[1] == "ธ.ค.") {
            $TMONTH = "12";
        }
        $TYEAR = substr("$TDATE[2]", 2);

        if (isset($TMONTH)) {
            if(strlen($TDATE[0]) == 2){
                $TDATE_0 =  $TDATE[0] ;
                $TDATETH = $TDATE[0] . "/" . $TMONTH . "/" . $TYEAR . " " . $TDATE[3];
                $TDATETHV2 = $TDATE_0 . $TMONTH . $TDATE[2] - 543;
            }else{
                $TDATE_0 = str_pad($TDATE[0], 2, "0", STR_PAD_LEFT);
                $TDATETHSET = strval($TDATE[0] . "/" . $TMONTH . "/" . $TYEAR . " " . $TDATE[3]);
                $TDATETHV2SET = strval($TDATE_0 . $TMONTH . $TDATE[2] - 543);
                $TDATETH= "0".$TDATETHSET;
                $TDATETHV2 = "0".$TDATETHV2SET;
            }
        }
        //===========================================================

        //=========================== BRTHDATE  =========================
        $BRTHDATE = explode(" ", $User["BRTHDATE"]);
        if ($BRTHDATE[1] == "ม.ค.") {
            $BMONTH = "01";
        } else if ($BRTHDATE[1] == "ก.พ.") {
            $BMONTH = "02";
        } else if ($BRTHDATE[1] == "มี.ค.") {
            $BMONTH = "03";
        } else if ($BRTHDATE[1] == "เม.ย.") {
            $BMONTH = "04";
        } else if ($BRTHDATE[1] == "พ.ค.") {
            $BMONTH = "05";
        } else if ($BRTHDATE[1] == "มิ.ย.") {
            $BMONTH = "06";
        } else if ($BRTHDATE[1] == "ก.ค.") {
            $BMONTH = "07";
        } else if ($BRTHDATE[1] == "ส.ค.") {
            $BMONTH = "08";
        } else if ($BRTHDATE[1] == "ก.ย.") {
            $BMONTH = "09";
        } else if ($BRTHDATE[1] == "ต.ค.") {
            $BMONTH = "10";
        } else if ($BRTHDATE[1] == "พ.ย.") {
            $BMONTH = "11";
        } else if ($BRTHDATE[1] == "ธ.ค.") {
            $BMONTH = "12";
        }

        if (isset($BMONTH)) {
            if(strlen($BRTHDATE[0]) == 2){
                $TBRTHDATE =  $BRTHDATE[0] . "/" . $BMONTH . "/" . $BRTHDATE[2];
            }else{
                $TBRTHDATE = "0".$BRTHDATE[0] . "/" . $BMONTH . "/" . $BRTHDATE[2];
            }
        }

        //===========================================================

        //================== API_check_service_charge ===================
        if (isset($TDATETHV2)) {
            $API_Service_Charge = $this->API_check_service_charge($User["HN"], $TDATETHV2, $User["CLINICLCT"], $User["PTTYPE1"]);
            $API_ICD10ICD9_One = $this->API_icd10icd9_one($User["HN"], $TDATETHV2,"302030001");
            $API_ICD10ICD9_All = $this->API_icd10icd9_all($User["HN"], $TDATETHV2);

            if($API_ICD10ICD9_One->json_data == []){
                $DATAICD10ICD9_One = $this->API_icd10icd9_one($User["HN"], $TDATETHV2,"");
            }else{
                $DATAICD10ICD9_One = $this->API_icd10icd9_one($User["HN"], $TDATETHV2,"302030001");
            }
        }
        //============================================================

        if (!empty($API_Service_Charge) && !empty($API_ICD10ICD9_One) && !empty($API_ICD10ICD9_All)) {
            if ($API_Service_Charge->json_result == true) {
                //========================================================== ตั่งค่าหน้ากระดาษ =====================================================================
                $pdf = new \FPDF('P', 'mm', 'A4'); //(orientation, 'mm' , format)  สร้าง instant FPDF
                //^==================== orientation ==========================
                //P – แนวตั้ง (default)     //L – แนวนอน
                //^====================== format ============================
                //A3        //A4 (default)      //A5        //Letter        //array(width,height) – กำหนดเอง โดยส่งอะเรย์ กว้างxสูง
                $pdf->SetAuthor('RajavithiHospital'); //กำหนดชื่อเจ้าของเอกสาร
                $pdf->SetCreator('fpdf version 1.84'); //สำหรับกำหนดชื่อผู้สร้างเอกสาร โดยทั่วไปแล้วจะใช้เป็นชื่อแอพพลิเคชั่นที่สร้างไฟล์ pdf
                $pdf->SetDisplayMode('fullpage', 'single'); //(zoom ,layout)ไฟล์ pdf นั้นเวลาเปิด ให้เลือกว่าจะดูแบบ เต็มหน้ากระดาษ หรือเต็มความกว้างของหน้าจอ และอีกหลายตัวเลือก คำสั่งนี้อนุญาติให้เรากำหนดโหมดที่จะให้ user เห็นตั้งแต่เปิดเอกสาร
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
                $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php'); //เพิ่ม Font ธรรมดา
                $pdf->AddFont('THSarabunNew', 'B', 'THSarabunNew Bold.php'); //เพิ่ม Font หนา
                $pdf->AddFont('THSarabunNew', 'I', 'THSarabunNew Italic.php'); //เพิ่ม Font เอียง
                $pdf->AddFont('THSarabunNew', 'BI', 'THSarabunNew BoldItalic.php'); //เพิ่ม Font หนาเอี
                $pdf->SetSubject('this document for RJHCR.'); //สำหรับกำหนด subject ของเอกสาร
                $pdf->SetTitle('Medical Check Up Report'); //สำหรับกำหนด title ของเอกสาร
                $pdf->SetAutoPageBreak(false);// set a bottom margin in FPDF
                $pdf->AddPage();//เพิ่มหน้ากระดาษ
                //==========================================================================================================================================

                //==================================================== ตัวอย่าง =====================================================
                //        $pdf->SetFont('THSarabunNew','B',16); //กำหนดฟอนต์ Arial ตัวหนา ขนาด 16
                //        $pdf->Text( 10 , 14 , 'Hello World!'); //พิมพ์คำว่า Hello World! ลงไปในตำแหน่ง  //เยื้องจากขอบกระดาษด้านซ้าย 10 มม. //เยื้องจากขอบกระดาษด้านบน 10 มม.
                //===============================================================================================================

                //==================================================== วัดค่ากระดาษ =====================================================
                //                    $pdf->SetFont('THSarabunNew','B',16);
                //                    $pdf->Text( 11 , 3 , iconv( 'UTF-8','cp874' , '|1' ));
                //                    $pdf->Text( 21 , 3 , iconv( 'UTF-8','cp874' , '|2' ));
                //                    $pdf->Text( 31 , 3 , iconv( 'UTF-8','cp874' , '|3' ));
                //                    $pdf->Text( 41 , 3 , iconv( 'UTF-8','cp874' , '|4' ));
                //                    $pdf->Text( 51 , 3 , iconv( 'UTF-8','cp874' , '|5' ));
                //                    $pdf->Text( 61 , 3 , iconv( 'UTF-8','cp874' , '|6' ));
                //                    $pdf->Text( 71 , 3 , iconv( 'UTF-8','cp874' , '|7' ));
                //                    $pdf->Text( 81 , 3 , iconv( 'UTF-8','cp874' , '|8' ));
                //                    $pdf->Text( 91 , 3 , iconv( 'UTF-8','cp874' , '|9' ));
                //                    $pdf->Text( 101 , 3 , iconv( 'UTF-8','cp874' , '|10' ));
                //                    $pdf->Text( 111 , 3 , iconv( 'UTF-8','cp874' , '|11' ));
                //                    $pdf->Text( 121 , 3 , iconv( 'UTF-8','cp874' , '|12' ));
                //                    $pdf->Text( 131 , 3 , iconv( 'UTF-8','cp874' , '|13' ));
                //                    $pdf->Text( 141 , 3 , iconv( 'UTF-8','cp874' , '|14' ));
                //                    $pdf->Text( 151 , 3 , iconv( 'UTF-8','cp874' , '|15' ));
                //                    $pdf->Text( 161 , 3 , iconv( 'UTF-8','cp874' , '|16' ));
                //                    $pdf->Text( 171 , 3 , iconv( 'UTF-8','cp874' , '|17' ));
                //                    $pdf->Text( 181 , 3 , iconv( 'UTF-8','cp874' , '|18' ));
                //                    $pdf->Text( 191 , 3 , iconv( 'UTF-8','cp874' , '|19' ));
                //                    $pdf->Text( 201 , 3 , iconv( 'UTF-8','cp874' , '|20' ));
                //
                //                    $pdf->Text( 0 , 11 , iconv( 'UTF-8','cp874' , '__1' ));
                //                    $pdf->Text( 0 , 21 , iconv( 'UTF-8','cp874' , '__2' ));
                //                    $pdf->Text( 0 , 31 , iconv( 'UTF-8','cp874' , '__3' ));
                //                    $pdf->Text( 0 , 41 , iconv( 'UTF-8','cp874' , '__4' ));
                //                    $pdf->Text( 0 , 51 , iconv( 'UTF-8','cp874' , '__5' ));
                //                    $pdf->Text( 0 , 61 , iconv( 'UTF-8','cp874' , '__6' ));
                //                    $pdf->Text( 0 , 71 , iconv( 'UTF-8','cp874' , '__7' ));
                //                    $pdf->Text( 0 , 81 , iconv( 'UTF-8','cp874' , '__8' ));
                //                    $pdf->Text( 0 , 91 , iconv( 'UTF-8','cp874' , '__9' ));
                //                    $pdf->Text( 0 , 101 , iconv( 'UTF-8','cp874' , '__10' ));
                //                    $pdf->Text( 0 , 111 , iconv( 'UTF-8','cp874' , '__11' ));
                //                    $pdf->Text( 0 , 121 , iconv( 'UTF-8','cp874' , '__12' ));
                //                    $pdf->Text( 0 , 131 , iconv( 'UTF-8','cp874' , '__13' ));
                //                    $pdf->Text( 0 , 141 , iconv( 'UTF-8','cp874' , '__14' ));
                //                    $pdf->Text( 0 , 151 , iconv( 'UTF-8','cp874' , '__15' ));
                //                    $pdf->Text( 0 , 161 , iconv( 'UTF-8','cp874' , '__16' ));
                //                    $pdf->Text( 0 , 171 , iconv( 'UTF-8','cp874' , '__17' ));
                //                    $pdf->Text( 0 , 181 , iconv( 'UTF-8','cp874' , '__18' ));
                //                    $pdf->Text( 0 , 191 , iconv( 'UTF-8','cp874' , '__19' ));
                //                    $pdf->Text( 0 , 201 , iconv( 'UTF-8','cp874' , '__20' ));
                //                    $pdf->Text( 0 , 211 , iconv( 'UTF-8','cp874' , '__21' ));
                //                    $pdf->Text( 0 , 221 , iconv( 'UTF-8','cp874' , '__22' ));
                //                    $pdf->Text( 0 , 231 , iconv( 'UTF-8','cp874' , '__23' ));
                //                    $pdf->Text( 0 , 241 , iconv( 'UTF-8','cp874' , '__24' ));
                //                    $pdf->Text( 0 , 251 , iconv( 'UTF-8','cp874' , '__25' ));
                //                    $pdf->Text( 0 , 261 , iconv( 'UTF-8','cp874' , '__26' ));
                //                    $pdf->Text( 0 , 271 , iconv( 'UTF-8','cp874' , '__27' ));
                //                    $pdf->Text( 0 , 281 , iconv( 'UTF-8','cp874' , '__28' ));
                //                    $pdf->Text( 0 , 291 , iconv( 'UTF-8','cp874' , '__29' ));
                //===============================================================================================================


                //=================================================== Report ================================================================
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(9, 21, iconv('UTF-8', 'cp874', 'สรุปรายการรักษาพยาบาลผู้ป่วยใน เพื่อตั้งเบิกสกส.'));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(115, 21, iconv('UTF-8', 'cp874', 'วันที่ร่าง'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(128, 21, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(158, 21, iconv('UTF-8', 'cp874', 'วันที่พิมพ์'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(173, 21, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));

                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(9, 28, iconv('UTF-8', 'cp874', 'H.N.'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(16, 28, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(17, 28, iconv('UTF-8', 'cp874', $HN_Set3));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(38, 28, iconv('UTF-8', 'cp874', 'A.N.'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(46, 28, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(47, 28, iconv('UTF-8', 'cp874', 'ODS??????'));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(68, 28, iconv('UTF-8', 'cp874', 'ชื่อ สกุล ผู้ป่วย'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(90, 28, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(91, 28, iconv('UTF-8', 'cp874', $User["NAME"]));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(137, 28, iconv('UTF-8', 'cp874', 'วันเวลาที่มารับบริการ'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(169, 28, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _'));
                $pdf->SetFont('THSarabunNew', '', 15);
                if (isset($TDATETH)) {
                    $pdf->Text(170, 28, iconv('UTF-8', 'cp874', $TDATETH));
                }

                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(9, 35, iconv('UTF-8', 'cp874', 'สิทธิการรักษาผู้ป่วย'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(40, 35, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(41, 35, iconv('UTF-8', 'cp874', $User["PTTYPE_NM"]));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(119, 35, iconv('UTF-8', 'cp874', 'หน่วยงาน'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(135, 35, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _  '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(136, 35, iconv('UTF-8', 'cp874', "(" . $User["CLAIMLCT"] . ")" . " " . $User["CLAIMLCTN"]));

                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(9, 42, iconv('UTF-8', 'cp874', 'เลขประจำตัวบัตรประชาชน-ผู้ป่วย'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(62, 42, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(67, 42, iconv('UTF-8', 'cp874', $User["CARDNO"]));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(110, 42, iconv('UTF-8', 'cp874', 'วัน-เดือน-ปี เกิด'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(135, 42, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 16);
                if (isset($TBRTHDATE)) {
                    $pdf->Text(137, 42, iconv('UTF-8', 'cp874', $TBRTHDATE));
                }
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(157, 42, iconv('UTF-8', 'cp874', 'อายุ'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(164, 42, iconv('UTF-8', 'cp874', '_ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(166, 42, iconv('UTF-8', 'cp874', $User["AGE"]));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(173, 42, iconv('UTF-8', 'cp874', 'ปี'));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(178, 42, iconv('UTF-8', 'cp874', 'เพศ'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(185, 42, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(189, 42, iconv('UTF-8', 'cp874', $User["MALEN"]));

                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(9, 49, iconv('UTF-8', 'cp874', 'สัญชาติ'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(22, 49, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(29, 49, iconv('UTF-8', 'cp874', $User["NTNLTYN"]));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(49, 49, iconv('UTF-8', 'cp874', 'สถานภาพ'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(66, 49, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(79, 49, iconv('UTF-8', 'cp874', $User["MRTLST"]));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(110, 49, iconv('UTF-8', 'cp874', 'อาชีพ'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(119, 49, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(125, 49, iconv('UTF-8', 'cp874', $User["OCCPTN"]));

                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(9, 56, iconv('UTF-8', 'cp874', 'น้ำหนักแรกรับ(kg)'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(38, 56, iconv('UTF-8', 'cp874', '_ _ _ _ _ _'));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(49, 56, iconv('UTF-8', 'cp874', 'สถานภาพจำหน่าย'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(78, 56, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(131, 56, iconv('UTF-8', 'cp874', 'ประเภทจำหน่าย'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(157, 56, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));

                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(9, 63, iconv('UTF-8', 'cp874', 'แผนก'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(19, 63, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _'));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(55, 63, iconv('UTF-8', 'cp874', 'WARD'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(66, 63, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _'));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(110, 63, iconv('UTF-8', 'cp874', 'วันนอน'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(123, 63, iconv('UTF-8', 'cp874', '_ _ _ _'));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(131, 63, iconv('UTF-8', 'cp874', 'วัน'));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(137, 63, iconv('UTF-8', 'cp874', 'วันเวลาที่จำหน่าย'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(165, 63, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _'));

                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(9, 70, iconv('UTF-8', 'cp874', 'แพทย์เจ้าของไข้'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(34, 70, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(110, 70, iconv('UTF-8', 'cp874', 'สาเหตุที่รับไว้'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(131, 70, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));

                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(9, 77, iconv('UTF-8', 'cp874', 'DRG'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(17, 77, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(110, 77, iconv('UTF-8', 'cp874', 'RW'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(116, 77, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(154, 77, iconv('UTF-8', 'cp874', 'AdjRW'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(166, 77, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));

                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(55, 86, iconv('UTF-8', 'cp874', 'รายการค่าบริการทางการแพทย์'));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(145, 86, iconv('UTF-8', 'cp874', 'เบิกได้'));
                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(176, 86, iconv('UTF-8', 'cp874', 'เบิกไม่ได้'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 95, iconv('UTF-8', 'cp874', '1.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 95, iconv('UTF-8', 'cp874', 'ค่าห้อง / ค่าอาหาร'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 102, iconv('UTF-8', 'cp874', '2.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 102, iconv('UTF-8', 'cp874', 'ค่าอวัยวะเทียม / อุปกรณ์ในการบำบัดรักษาโรค'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 109, iconv('UTF-8', 'cp874', '3.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 109, iconv('UTF-8', 'cp874', 'ค่ายาและสารอาหารทางเส้นเลือดที่ใช้ใน รพ.'));
//                var_dump($Data_Service_Charge);die();
                if($API_Service_Charge->json_data[0]->GRP90 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 109 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 109 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP90, 2)));
                }
                if($API_Service_Charge->json_data[0]->GRP91 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 177 , 109 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew','',15);
                    $pdf->Text( 177 , 109 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP91, 2)));
                }

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 116, iconv('UTF-8', 'cp874', '4.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 116, iconv('UTF-8', 'cp874', 'ค่ายากลับบ้าน'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 123, iconv('UTF-8', 'cp874', '5.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 123, iconv('UTF-8', 'cp874', 'ค่าเวชภัณฑ์ที่มิใช่ยา'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 130, iconv('UTF-8', 'cp874', '6.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 130, iconv('UTF-8', 'cp874', 'ค่าบริการ โลหิตและส่วนประกอบของโลหิต'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 137, iconv('UTF-8', 'cp874', '7.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 137, iconv('UTF-8', 'cp874', 'ค่าตรวจวินิจฉัยทางเทคนิคการแพทย์และพยาธิวิทยา'));
                if($API_Service_Charge->json_data[0]->GRP30 == null && $API_Service_Charge->json_data[0]->GRP10 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 137 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 137 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP30+$API_Service_Charge->json_data[0]->GRP10, 2)));
                }
                if($API_Service_Charge->json_data[0]->GRP31 == null && $API_Service_Charge->json_data[0]->GRP11 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 177 , 137 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew','',15);
                    $pdf->Text( 177 , 137 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP31+$API_Service_Charge->json_data[0]->GRP11, 2)));
                }

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 144, iconv('UTF-8', 'cp874', '8.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 144, iconv('UTF-8', 'cp874', 'ค่าตรวจวินิจฉัยและรักษาทางรังสีวิทยา'));
                if($API_Service_Charge->json_data[0]->GRP20 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 144 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 144 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP20, 2)));
                }
                if($API_Service_Charge->json_data[0]->GRP21 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 177 , 144 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew','',15);
                    $pdf->Text( 177 , 144 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP21, 2)));
                }

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 151, iconv('UTF-8', 'cp874', '9.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 151, iconv('UTF-8', 'cp874', 'ค่าตรวจวินิจฉัยโดยวิธีพิเศษอื่นๆ'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 158, iconv('UTF-8', 'cp874', '10.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 158, iconv('UTF-8', 'cp874', 'ค่าอุปกรณ์ของใช้และเครื่องมือทางการแพทย์'));
                if($API_Service_Charge->json_data[0]->GRP60 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 158 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 158 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP60, 2)));
                }
                if($API_Service_Charge->json_data[0]->GRP61 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 177 , 158 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew','',15);
                    $pdf->Text( 177 , 158 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP61, 2)));
                }

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 165, iconv('UTF-8', 'cp874', '11.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 165, iconv('UTF-8', 'cp874', 'ค่าผ่าตัด ทำคลอด ทำหัตถการและบริการวิสัญญี'));
                if($API_Service_Charge->json_data[0]->GRP40 == null && $API_Service_Charge->json_data[0]->GRP50 == null ){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text(145, 165, iconv('UTF-8', 'cp874', ""));
                }else{
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text(145, 165, iconv('UTF-8', 'cp874', number_format($API_Service_Charge->json_data[0]->GRP40+$API_Service_Charge->json_data[0]->GRP50, 2)));
                }
                if($API_Service_Charge->json_data[0]->GRP41 == null && $API_Service_Charge->json_data[0]->GRP51 == null ){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text(177, 165, iconv('UTF-8', 'cp874', ""));
                }else{
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text(177, 165, iconv('UTF-8', 'cp874', number_format($API_Service_Charge->json_data[0]->GRP41+$API_Service_Charge->json_data[0]->GRP51, 2)));
                }

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 172, iconv('UTF-8', 'cp874', '12.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 172, iconv('UTF-8', 'cp874', 'ค่าบริการทางการพยาบาล'));
                if($API_Service_Charge->json_data[0]->GRP110 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 172 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 145 , 172 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP110, 2)));
                }
                if($API_Service_Charge->json_data[0]->GRP111 == null){
                    $pdf->SetFont('THSarabunNew', '', 15);
                    $pdf->Text( 177 , 172 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew','',15);
                    $pdf->Text( 177 , 172 , iconv( 'UTF-8','cp874' , number_format($API_Service_Charge->json_data[0]->GRP111, 2)));
                }

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 179, iconv('UTF-8', 'cp874', '13.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 179, iconv('UTF-8', 'cp874', 'ค่าบริการทันตกรรม'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 186, iconv('UTF-8', 'cp874', '14.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 186, iconv('UTF-8', 'cp874', 'ค่าบริการทางกายภาพบำบัดและทางเวชกรรมฟื้นฟู'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 193, iconv('UTF-8', 'cp874', '15.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 193, iconv('UTF-8', 'cp874', 'ค่าบริการฝังเข็ม / การบำบัดของผู้ประกอบโรคศิลปะอื่นฯ'));

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 200, iconv('UTF-8', 'cp874', '16.'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(23, 200, iconv('UTF-8', 'cp874', 'ค่าบริการอื่นที่ไม่เกี่ยวข้องกับการรักษา'));

                $pdf->SetFont('THSarabunNew', 'B', 16);
                $pdf->Text(73, 208, iconv('UTF-8', 'cp874', 'รวมทั้งสิ้น'));
                $Sum0 = number_format($API_Service_Charge->json_data[0]->GRP20+$API_Service_Charge->json_data[0]->GRP30+$API_Service_Charge->json_data[0]->GRP10+$API_Service_Charge->json_data[0]->GRP40+$API_Service_Charge->json_data[0]->GRP50
                    +$API_Service_Charge->json_data[0]->GRP60+$API_Service_Charge->json_data[0]->GRP90+$API_Service_Charge->json_data[0]->GRP110, 2);
                $Sum1 = number_format($API_Service_Charge->json_data[0]->GRP21+$API_Service_Charge->json_data[0]->GRP31+$API_Service_Charge->json_data[0]->GRP11+$API_Service_Charge->json_data[0]->GRP41+$API_Service_Charge->json_data[0]->GRP51
                    +$API_Service_Charge->json_data[0]->GRP61+$API_Service_Charge->json_data[0]->GRP91+$API_Service_Charge->json_data[0]->GRP111, 2);
                if($Sum0 == 0){
                    $pdf->SetFont('THSarabunNew', 'B', 15);
                    $pdf->Text( 145 , 208 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew', 'B', 15);
                    $pdf->Text( 145 , 208 , iconv( 'UTF-8','cp874' , $Sum0));
                }
                if($Sum1 == 0){
                    $pdf->SetFont('THSarabunNew', 'B', 15);
                    $pdf->Text( 177 , 208 , iconv( 'UTF-8','cp874' , ""));
                }else{
                    $pdf->SetFont('THSarabunNew','B',15);
                    $pdf->Text( 177 , 208 , iconv( 'UTF-8','cp874' , $Sum1));
                }

                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(13, 230, iconv('UTF-8', 'cp874', 'ลงชื่อ'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(21, 230, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(42, 236, iconv('UTF-8', 'cp874', '( นางสุรีย์รัตน์  แสงใส )'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(46, 244, iconv('UTF-8', 'cp874', 'เจ้าพนักงานธุรการ'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(110, 230, iconv('UTF-8', 'cp874', 'ลงชื่อ'));
                $pdf->SetFont('THSarabunNew', '', 10);
                $pdf->Text(118, 230, iconv('UTF-8', 'cp874', '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ '));
                $pdf->Image('../../web/RJDataExport/images/signature01.PNG', 140, 221, 30, 0);
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(134, 236, iconv('UTF-8', 'cp874', '( นายกนกพจน์ จันทร์ภิวัฒน์ )'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(114, 244, iconv('UTF-8', 'cp874', 'นายแพทย์ชำนาญการพิเศษ ด้านเวชกรรม สาขาอายุรกรรม'));
                $pdf->SetFont('THSarabunNew', '', 15);
                $pdf->Text(120, 252, iconv('UTF-8', 'cp874', 'ปฏิบัติราชการแทนผู้อำนวยการโรงพยาบาลราชวิถี'));

                //===============================================================================================================


                ///========================================================= กำหนดเส้นขอบ =======================================================================
                $pdf->SetTopMargin(0); //คำสั่งที่ใช้สำหรับ กำหนดกั้นหน้ากระดาษนั้น ประกอบด้วย
                //SetMargins( 50,30,10 ); คำสั่งนี้ใช้สำหรับกำหนดกั้นหน้ากระดาษ ซึ่งจะต้องเรียกใช้งานก่อนคำสั่ง AddPage (คำสั่งเพิ่มหน้ากระดาษ) โดยค่าดีฟอลต์แล้ว กั้นหน้ากระดาษทั้งซ้าย ขวา บน ล่าง จะถูกกำหนดไว้ที่ 1 ซม.
                //SetLeftMargin( 50 ); ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านซ้าย ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่
                //SetRightMargin( 50 ); ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านขวา ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่
                //SetTopMargin( 50 );  ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านบน หรือจะเรียกว่ากั้นหัวกระดาษ ก็ได้ ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่


                $pdf->SetY(80);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(11);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(187, 9, iconv('UTF-8', 'cp874', ''), 'LTRB');

                $pdf->SetY(89);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(11);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(0, 113, iconv('UTF-8', 'cp874', ''), 'L');
                $pdf->SetY(89);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(20);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(0, 113, iconv('UTF-8', 'cp874', ''), 'L');
                $pdf->SetY(80);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(135);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(50, 10, iconv('UTF-8', 'cp874', ''), 'L');
                $pdf->SetY(80);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(167);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(50, 10, iconv('UTF-8', 'cp874', ''), 'L');

                $pdf->SetY(89);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(135);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(0, 113, iconv('UTF-8', 'cp874', ''), 'L');
                $pdf->SetY(89);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(198);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(0, 113, iconv('UTF-8', 'cp874', ''), 'L');
                $pdf->SetY(90);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(167);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(63, 113, iconv('UTF-8', 'cp874', ''), 'L');

                $pdf->SetY(202);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(11);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(187, 9, iconv('UTF-8', 'cp874', ''), 'LTRB');
                $pdf->SetY(202);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(135);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(50, 9, iconv('UTF-8', 'cp874', ''), 'L');
                $pdf->SetY(202);//เลื่อนแกรน Y แนวตั้ง
                $pdf->SetX(167);//เลื่อนแกรน X แนวตนอน
                $pdf->MultiCell(50, 9, iconv('UTF-8', 'cp874', ''), 'L');

                //        $pdf->SetY(8);//เลื่อนแกรน Y แนวตั้ง
                //        $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                //        $pdf->MultiCell( 195  , 280 , iconv( 'UTF-8','cp874','' ),'LTRB');
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
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(60, 21, iconv('UTF-8', 'cp874', 'ใบแสดงการวินิจฉัย (ICD-10) และหัตถการ (ICD9-CM)'));

                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(9, 35, iconv('UTF-8', 'cp874', 'ชื่อ สกุล-ผู้ป่วย'));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(35, 35, iconv('UTF-8', 'cp874', $User["NAME"]));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(90, 35, iconv('UTF-8', 'cp874', 'HN'));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(100, 35, iconv('UTF-8', 'cp874', $HN_Set3));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(130, 35, iconv('UTF-8', 'cp874', 'วันที่รับบริการ'));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                if (isset($DATAICD10ICD9_One)) {
                    if($DATAICD10ICD9_One->json_data == []){
                        $pdf->Text(155, 35, iconv('UTF-8', 'cp874', ""));
                    }else{
                        $pdf->Text(155, 35, iconv('UTF-8', 'cp874', $DATAICD10ICD9_One->json_data[0]->VSTDATE));
                    }
                }


                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(9, 56, iconv('UTF-8', 'cp874', 'แพทย์ผู้สรุป'));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                if (isset($DATAICD10ICD9_One)) {
                    if($DATAICD10ICD9_One->json_data == []){
                        $pdf->Text(35, 56, iconv('UTF-8', 'cp874', ""));
                    }else{
                        $pdf->Text(35, 56, iconv('UTF-8', 'cp874', $DATAICD10ICD9_One->json_data[0]->DCTDSPNAME));
                    }
                }
                $pdf->SetFont('THSarabunNew', 'B', 15);
                $pdf->Text(90, 56, iconv('UTF-8', 'cp874', 'เลขที่ใบประกอบวิชาชีพ'));
                $pdf->SetFont('THSarabunNew', 'B', 15);
                if (isset($DATAICD10ICD9_One)) {
                    if($DATAICD10ICD9_One->json_data == []){
                        $pdf->Text(130, 56, iconv('UTF-8', 'cp874', ""));
                    }else{
                        $pdf->Text(130, 56, iconv('UTF-8', 'cp874', $DATAICD10ICD9_One->json_data[0]->LCNO));
                    }
                }


                $pdf->SetFont('THSarabunNew', 'B', 14);
                $pdf->Text(9, 66, iconv('UTF-8', 'cp874', '#  Other diag    #  Procedure                   Dr แพทย์                                Date St       Time St       Date Exp       Time Exp'));
                $conut_row = 73;
                foreach ($API_ICD10ICD9_All->json_data as  $data_icd10icd9){
                    $pdf->SetFont('THSarabunNew', '', 14);
                    if($data_icd10icd9->FLAG == "ICD10"){
                        $pdf->Text(9, $conut_row, iconv('UTF-8', 'cp874','     '.$data_icd10icd9->DIAG));
                        $pdf->Text(67, $conut_row, iconv('UTF-8', 'cp874',$data_icd10icd9->DCTDSPNAME));
                        $pdf->Text(122, $conut_row, iconv('UTF-8', 'cp874',$data_icd10icd9->VSTDATE.'     '.$data_icd10icd9->DTIME));
                        $conut_row = $conut_row+7;
                    }else{
                        $pdf->Text(9, $conut_row, iconv('UTF-8', 'cp874','                             '.$data_icd10icd9->DIAG));
                        $pdf->Text(67, $conut_row, iconv('UTF-8', 'cp874',$data_icd10icd9->DCTDSPNAME));
                        $pdf->Text(122, $conut_row, iconv('UTF-8', 'cp874',$data_icd10icd9->VSTDATE.'     '.$data_icd10icd9->DTIME));
                        $conut_row = $conut_row+7;
                    }
                }


                //===========================================================================================================================


                ///========================================================= กำหนดเส้นขอบ =======================================================================
                $pdf->SetTopMargin(0); //คำสั่งที่ใช้สำหรับ กำหนดกั้นหน้ากระดาษนั้น ประกอบด้วย
                //SetMargins( 50,30,10 ); คำสั่งนี้ใช้สำหรับกำหนดกั้นหน้ากระดาษ ซึ่งจะต้องเรียกใช้งานก่อนคำสั่ง AddPage (คำสั่งเพิ่มหน้ากระดาษ) โดยค่าดีฟอลต์แล้ว กั้นหน้ากระดาษทั้งซ้าย ขวา บน ล่าง จะถูกกำหนดไว้ที่ 1 ซม.
                //SetLeftMargin( 50 ); ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านซ้าย ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่
                //SetRightMargin( 50 ); ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านขวา ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่
                //SetTopMargin( 50 );  ใช้สำหรับกำหนดกั้นหน้ากระดาษด้านบน หรือจะเรียกว่ากั้นหัวกระดาษ ก็ได้ ให้กำหนดไว้ก่อนสร้างหน้ากระดาษใหม่


                //        $pdf->SetY(8);//เลื่อนแกรน Y แนวตั้ง
                //        $pdf->SetX(8);//เลื่อนแกรน X แนวตนอน
                //        $pdf->MultiCell( 195  , 280 , iconv( 'UTF-8','cp874','' ),'LTRB');
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
            } else {
                Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
                return $this->redirect(['login/login_his']);
            }
        }
    }
}