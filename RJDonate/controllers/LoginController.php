<?php
namespace RJDonate\controllers;

use RJDonate\models\LoginForm;
use Yii;

class LoginController extends \yii\web\Controller
{
    public function actionLogin_his()
    {
        $this->layout = '@app/../themes/login-template-1/views/layouts/login_RJDonate';
        $LoginModel = new LoginForm();

//        $LoginModel->username = 'apiauthen';
//        $LoginModel->password = '9ixLZfoBOxovZgk@91';

        Yii::$app->session->set('access_token',"");

        if(Yii::$app->request->post()){
            $LoginModel->load(Yii::$app->request->post());
            if($LoginModel->username == "" || $LoginModel->password == ""){
                $LoginModel->validate();
            }else{
//                echo '<pre>';
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
                    $chk_expdate = $payload->staff_lct;
                    if($payload->staff_idcrd == null){
                        $USER_ID = $payload->staff;
                    }else{
                        $USER_ID = $payload->staff_idcrd;
                    }
                    $VIEW_DATE = $date = date("d/m/Y");
                    $PROJECT_ID = "006"; //เปลี่ยนตามรหัสโปรเจค
                    //===========================================

//                    if($chk_expdate == '2190100' || $chk_expdate == '3070100' ||   $chk_expdate == '2110000'){
                    //===================== API RiskReport_Admin ========================
                    $API_donate_admin = $this->API_donate_admin($payload->staff_idcrd,"",$obj_access_token->access_token);
                    //================= เก็บ session แบบ Array ข้อมูลหลายชุด =======================
                    $arrayAccess_Token = array();
                    $arrayAccess_Token['admin'] = $API_donate_admin;
                    $arrayAccess_Token['user'] = $payload;
                    $arrayAccess_Token['access_token'] = $obj_access_token->access_token;
                    Yii::$app->session->set('arrayAccess_Token',$arrayAccess_Token);
                    //============================= API Stack ============================
                    $this->API_stack($USER_ID,$VIEW_DATE,$PROJECT_ID);
                    //==================================================================
                    return $this->redirect(['donation/index']);
//                    }else{
//                        Yii::$app->session->setFlash('error',   'คุณไม่มีสิทธิ์ข้าใช้งาน');
//                    }
                }else{
                    Yii::$app->session->setFlash('error',   'Username/Password ไม่ถูกต้อง');
                }
            }
        }

        return $this->render('login_his', [
            'model' => $LoginModel,
            //ทำการ json_decode(...)ก่อน
        ]);
    }

    public function actionLogin_hr()
    {
        $this->layout = '@app/../themes/login-template-1/views/layouts/login';
        $LoginModel = new LoginForm();

        if(Yii::$app->request->post()){
            $LoginModel->load(Yii::$app->request->post());
            if($LoginModel->username == "" || $LoginModel->password == ""){
                $LoginModel->validate();
            }else{
//                echo '<pre>';
                $Token_username = md5($LoginModel->username);
                $Token_password = md5($LoginModel->password);
                $Key = $LoginModel->username ;
                $KeyCut = substr($Key, 0,-9);
                $Today = date("Y/m/d");
                $TodayCut = explode( "/",$Today);
                $Day = $TodayCut[2];
                $Month = $TodayCut[1];
                $Year = $TodayCut[0];
                $Token = md5($KeyCut.$Year.$Month.$Day.'rjvt');
                $DataToken = "utoken=$Token_username&ptoken=$Token_password&tmptoken=$Token";

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://hrws.rajavithi.go.th/mvc/human/xhrGetDetailsx',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $DataToken,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded',
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $obj_access_token = json_decode($response);
//                die();
                if($obj_access_token->json_result == true){
                    //แบบไม่ส่งค่า
                    return $this->redirect( ['site/index']);
                    //แบบส่งค่า
                    //return $this->redirect( ['site/index','aaa'=>$response]);
//                    var_dump($obj_access_token->json_result);
                }else{
                    Yii::$app->session->setFlash('error', 'Username หรือ Password <br> ไม่ถูกต้องกรุณาตรวจสอบ');
                }
            }
        }

        return $this->render('login_hr', [
            'model' => $LoginModel,
        ]);
    }

    public function API_stack($USER_ID,$VIEW_DATE,$PROJECT_ID){
        $curl = curl_init();
        $DataToken = 'USER_ID='.$USER_ID.'&VIEW_DATE='.$VIEW_DATE.'&PROJECT_ID='.$PROJECT_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/stack/stack',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $DataToken,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.Yii::$app->session->get('access_token'),
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

}