<?php
namespace RJFamily\controllers;

use RJFamily\models\LoginForm;
use Yii;

class LoginController extends \yii\web\Controller
{
    public function actionLogin_his()
    {
        $this->layout = '@app/../themes/login-template-0/views/layouts/login';

        $result = null ;
        $obj_access_token = null;
        $LoginModel = new LoginForm();

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
                    //"user": "apiauthen",
                    //"pwd": "9ixLZfoBOxovZgk@91"

                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));
                $response = curl_exec($curl);
                $obj_access_token = json_decode($response);
                curl_close($curl);

                if($obj_access_token == null){
                    Yii::$app->session->setFlash('error',   'พบปัญหาการเชื่อมต่อกับ server กรุณาตรวจสอบ');
                }else{
                    if($obj_access_token->json_result == true){
                        //แบบไม่ส่งค่า
                        return $this->redirect( ['admin/admin_index']);
                        //แบบส่งค่า
                        //return $this->redirect( ['site/index','aaa'=>$response]);
//                    var_dump($obj_access_token->json_result);
                    }else{
                        Yii::$app->session->setFlash('error', 'Username หรือ Password <br> ไม่ถูกต้องกรุณาตรวจสอบ');
                    }
                }

            }
        }

        return $this->render('login_his', [
            'model' => $LoginModel,
            //ทำการ json_decode(...)ก่อน
            'access_token' => $obj_access_token,
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
}