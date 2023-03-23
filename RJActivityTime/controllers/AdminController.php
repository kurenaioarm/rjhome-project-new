<?php
namespace RJActivityTime\controllers;
use RJActivityTime\models\StatuserForm;
use \yii\web\Controller;
use Yii;

class AdminController extends Controller
{
    public function actionAdmin_index()
    {
        //=============== การใช้ session=================
        //Yii::$app->session->get('access_token'); //session นำมาใช้
        //Yii::$app->session->set('access_token'); //session เก็บ
        //==========================================

        $StatuserModel = new StatuserForm();
        $API = null;
        $dateS = date("d/m/Y", strtotime("first day of this month"));
        $dateE = date("d/m/Y", strtotime("last day of this month"));
        $year = date("Y");
        $yearS = "01/01/".$year;
        $yearE = "31/12/".$year;
        //======================= API ESI Check Token==============
        $API = $this->API_event_date($yearS, $yearE);
        //=======================================================

        if ($API->json_result == true) {
            //=================================================================================================================
            if (Yii::$app->request->post()) {
                $StatuserModel->load(Yii::$app->request->post());
                $SelectedDate = $StatuserModel->SelectedDate;
                if ($SelectedDate == "") { // Check วันที่ ส่งมา
//                    Yii::$app->session->setFlash('error', 'กรุณาระบุวันที่');
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
                        if ($datecutS[2] == $datecutE[2] && $datecutS[1] == $datecutE[1] || $SUMDayCheck <= 30) { // Check ว่าเป็น เดือนเดียว ปีเดียว กันไหม
                            //========================== API event_date =========================
                            $API = $this->API_event_date($dateS, $dateE);
//                            var_dump(Yii::$app->request->post('Pttype_ID'));die();
                            //================================================================
                        } else {
                            $API = null;
                            Yii::$app->session->setFlash('error', 'ไม่สามารถตรวจสอบข้อมูลได้ <b><u>วันที่เลือกย้อนหลังต้องไม่เกิน 30 วัน</u></b> กรุณาระบุวันที่ใหม่อีกครั้ง');
                        }
                    } else {
                        //========================== API event_date =========================
                        var_dump(Yii::$app->request->post());die();
                        //================================================================
                    }
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
            return $this->redirect(['login/login_his']);
        }

        return $this->render('admin_index', [
            'model' => $StatuserModel,
            'API' => $API,
            'dateS' => $dateS,
            'dateE' => $dateE,
        ]);
    }

    public function actionCancelevent()
    {
        $currentToken = Yii::$app->session->get('access_token');
        $arrToken = explode('.', $currentToken);
        $arrTokenDecode = array();
        $arrTokenDecode['Header'] = $arrToken[0];
        $arrTokenDecode['Payload'] = $arrToken[1];
        $arrTokenDecode['Signature'] = $arrToken[2];
        $payload = json_decode(base64_decode($arrTokenDecode['Payload']));
        $chk_expdate = $payload->staff_lct;

        $dateT = date("d/m/Y");
        $SelectedDateCut = explode("-", Yii::$app->request->post('Data_date'));
        $dateS = $SelectedDateCut[0];
        $dateE = $SelectedDateCut[1];

        //========================== API event_cancel =======================
        $this->API_event_cancel(Yii::$app->request->post('Data_ACTIVITY_ID'), $dateT,$chk_expdate);
        //========================== API event_date =========================
        $API = $this->API_event_date($dateS, $dateE);
        //================================================================
        return $this->renderAjax('_Incident_cencel', [
            'API' => $API,
            'dateS' => $dateS,
            'dateE' => $dateE,
        ]);
    }

    public function actionUpdateevent()
    {
        $currentToken = Yii::$app->session->get('access_token');
        $arrToken = explode('.', $currentToken);
        $arrTokenDecode = array();
        $arrTokenDecode['Header'] = $arrToken[0];
        $arrTokenDecode['Payload'] = $arrToken[1];
        $arrTokenDecode['Signature'] = $arrToken[2];
        $payload = json_decode(base64_decode($arrTokenDecode['Payload']));
        $chk_expdate = $payload->staff_lct;

        $dateT = date("d/m/Y H:i:s");
        $SelectedDateCut = explode("-", Yii::$app->request->post('DateA'));
        $dateS = $SelectedDateCut[0];
        $dateE = $SelectedDateCut[1];

        //========================== API event_update =======================
        $this->API_event_update(Yii::$app->request->post('Data_Title'),Yii::$app->request->post('Data_date'),Yii::$app->request->post('Startdate'),
            Yii::$app->request->post('Enddate'),Yii::$app->request->post('Bgcolor'),Yii::$app->request->post('Link'),$dateT,$chk_expdate,Yii::$app->request->post('Activity_id'));
        //========================== API event_date =========================
        $API = $this->API_event_date($dateS, $dateE);
        //================================================================
        return $this->renderAjax('_Incident_cencel', [
            'API' => $API,
            'dateS' => $dateS,
            'dateE' => $dateE,
        ]);
    }

    public function actionInsertevent()
    {
        $currentToken = Yii::$app->session->get('access_token');
        $arrToken = explode('.', $currentToken);
        $arrTokenDecode = array();
        $arrTokenDecode['Header'] = $arrToken[0];
        $arrTokenDecode['Payload'] = $arrToken[1];
        $arrTokenDecode['Signature'] = $arrToken[2];
        $payload = json_decode(base64_decode($arrTokenDecode['Payload']));
        $chk_expdate = $payload->staff_lct;

        $dateT = date("d/m/Y H:i:s");
        $SelectedDateCutS = explode(" ", Yii::$app->request->post('Startdate'));
        $dateS = $SelectedDateCutS[0];
        $SelectedDateCutE = explode(" ", Yii::$app->request->post('Enddate'));
        $dateE = $SelectedDateCutE[0];

        //========================== API event_all =======================
        $Data_event_all =  $this->API_event_all();
        $Data_total = $Data_event_all->json_total + 1;
        //========================== API event_Insert =======================
        $this->API_event_Insert($Data_total,Yii::$app->request->post('Data_Title'),Yii::$app->request->post('Data_date'),Yii::$app->request->post('Startdate'),
            Yii::$app->request->post('Enddate'),Yii::$app->request->post('Bgcolor'),Yii::$app->request->post('Link'),$dateT,$chk_expdate);
        //========================== API event_date =========================
        $API = $this->API_event_date($dateS, $dateE);
        //================================================================
        return $this->renderAjax('_Incident_cencel', [
            'API' => $API,
            'dateS' => $dateS,
            'dateE' => $dateE,
        ]);
    }

    public function API_event_all()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjactivitytime_api/event_all',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . Yii::$app->session->get('access_token'),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function API_event_date($SDATE, $EDATE)
    {
        $curl = curl_init();
        $DataToken = 'SDATE=' . $SDATE . '&EDATE=' . $EDATE;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjactivitytime_api/event_date',
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

    public function API_event_cancel($ACTIVITY_ID, $TDATE, $CANCELSTF)
    {
        $curl = curl_init();
        $DataToken = 'ACTIVITY_ID=' . $ACTIVITY_ID . '&TDATE=' . $TDATE . '&CANCELSTF=' . $CANCELSTF ;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjactivitytime_api/event_cancel',
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

    public function API_event_update($ACTIVITY_TITLE, $DESCRIPTION, $STARTDATE, $ENDDATE, $ACTIVITY_BGCOLOR, $ACTIVITY_LINK,$FIRSTDATE,$FIRSTSTF, $ACTIVITY_ID)
    {
        $curl = curl_init();
        $DataToken = 'ACTIVITY_TITLE=' . $ACTIVITY_TITLE . '&DESCRIPTION=' . $DESCRIPTION . '&STARTDATE=' . $STARTDATE . '&ENDDATE=' . $ENDDATE . '&ACTIVITY_BGCOLOR=' . $ACTIVITY_BGCOLOR . '&ACTIVITY_LINK=' . $ACTIVITY_LINK. '&FIRSTDATE=' . $FIRSTDATE . '&FIRSTSTF=' . $FIRSTSTF  . '&ACTIVITY_ID=' . $ACTIVITY_ID ;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjactivitytime_api/event_update',
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

    public function API_event_Insert($ACTIVITY_ID,$ACTIVITY_TITLE, $DESCRIPTION, $STARTDATE, $ENDDATE, $ACTIVITY_BGCOLOR, $ACTIVITY_LINK,$FIRSTDATE,$FIRSTSTF)
    {
        $curl = curl_init();
        $DataToken =   'ACTIVITY_ID=' . $ACTIVITY_ID . '&ACTIVITY_TITLE=' . $ACTIVITY_TITLE . '&DESCRIPTION=' . $DESCRIPTION . '&STARTDATE=' . $STARTDATE . '&ENDDATE=' . $ENDDATE . '&ACTIVITY_BGCOLOR=' . $ACTIVITY_BGCOLOR . '&ACTIVITY_LINK=' . $ACTIVITY_LINK. '&FIRSTDATE=' . $FIRSTDATE . '&FIRSTSTF=' . $FIRSTSTF ;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjactivitytime_api/event_Insert',
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

}