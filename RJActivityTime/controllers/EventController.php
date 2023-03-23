<?php
namespace RJActivityTime\controllers;
use RJActivityTime\models\LoginForm;
use Symfony\Component\EventDispatcher\Event;
use \yii\web\Controller;
use Yii;

class EventController extends Controller
{
    public function actionEvent_index()
    {

        $events = [];
        $Data_date_event = [];

        //=========================== API event_two_year =============================
        $Data_allevents= $this->API_event_two_year();
        //        var_dump($Data_allevents);die();
        //===========================================================================

        //============================ yii2 fullcalendar =================================
        foreach ($Data_allevents->json_data AS $Data_allevent){
            if($Data_allevent->CANCELDATE == null){
                $Event = new \yii2fullcalendar\models\Event();
                $Event->id = $Data_allevent->ACTIVITY_ID;
                $Event->title = $Data_allevent->ACTIVITY_TITLE;
                $Event->className = 'display-10';//class
                $Event->backgroundColor = $Data_allevent->ACTIVITY_BGCOLOR;
                $Event->start = date('Y-m-d',strtotime($Data_allevent->STARTDATE));
                $Event->end = date('Y-m-d',strtotime('+1 day',strtotime($Data_allevent->ENDDATE)));
                $events[] = $Event;
            }
        }
        //===========================================================================

        return $this->render('event_index', [
            'events' => $events,
            'Data_date_event' => $Data_date_event
        ]);
    }

    public function API_timeline_event($ACTIVITY_ID){
        $curl = curl_init();
        $DataToken = 'ACTIVITY_ID='.$ACTIVITY_ID;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjactivitytime_api/timeline_date',
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

    public function API_timeline_date($SDATE){
        $curl = curl_init();
        $DataToken = 'SDATE='.$SDATE;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjactivitytime_api/timeline_event',
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

    public function API_event_two_year(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/rjactivitytime_api/event_two_year',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function actionEventday()
    {
        $Get_Data_date = Yii::$app->request->post("Day")."/".Yii::$app->request->post("Name_month")."/".Yii::$app->request->post("Year");
        //=========================== API event_two_year =============================
        $Data_date= $this->API_timeline_date("$Get_Data_date");
        //        var_dump($Data_date);die();
        //===========================================================================
        return $this->renderAjax('_Incident_Eventgroup', [
            'Data_date_event' => $Data_date,
        ]);
    }

    public function actionEvent()
    {
        //=========================== API event_two_year =============================
        $Data_event= $this->API_timeline_event(Yii::$app->request->post("Even_id"));
        //        var_dump($Data_event);die();
        //===========================================================================
        return $this->renderAjax('_Incident_Eventgroup', [
            'Data_date_event' => $Data_event,
        ]);
    }
}