<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>API Checker manual</title>
    <script src="jquery_3.3.1/dist/jquery.min.js"></script>
    <script src="countdown/dist/jquery.countdown.js"></script>
</head>

<body>
    <?php
    date_default_timezone_set('Asia/Bangkok');
    function notify($headerText, $contentText)
    {
        $appAndroid = 'f3ed969f-6d0f-448a-a1b5-1020d40806cf';
        $appIosid = '19c21f2b-a8a9-4b00-ac5a-aa9a2f351e52';

        $authorizAndroid = 'MzE2ZWFlYjQtNjE5NC00ZDNkLTk3MWUtY2FmYjQxOTZhYjA1';
        $authorizIos = 'NzNkMzBhNjItODI2Yi00MGE5LTg5MWYtMDE5Njg0NmMxYTZh';

        $responseArray = array();

        $heading = array("en" => $headerText);
        $content = array("en" => $contentText);
        $userNotiDetails = array(
            'result' => true,
            'data' => array(
                'notify_operatingsystem' => 'Ios',
                'notify_regid' => '65b6248d-56cb-4ea5-8725-0e78e0c19705'
            )
        );
        if ($userNotiDetails['result']) {
            $notiObj = (object) $userNotiDetails['data'];
            if ($notiObj->notify_operatingsystem == 'Ios') {
                $currentAppID = $appIosid;
                $currentAuthorize = $authorizIos;
            } elseif ($notiObj->notify_operatingsystem == 'Android') {
                $currentAppID = $appAndroid;
                $currentAuthorize = $authorizAndroid;
            }
            $currentMobileID = $notiObj->notify_regid;

            $fields = array(
                'app_id' => $currentAppID,
                'include_player_ids' => [$currentMobileID],
                'data' => array("foo" => "bar"),
                'url' => 'https://hrws.rajavithi.go.th/mvc/',
                'contents' => $content,
                'headings' => $heading
            );

            $jsonFields = json_encode($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $currentAuthorize
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonFields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            $response = false;
        }

        if ($response) {
            $responseArray['json_result'] = TRUE;
            $responseArray['json_data'] = array('action' => 'Notify Compleate');
        } else {
            $responseArray['json_result'] = FALSE;
            $responseArray['json_data'] = array('action' => 'Notify Failed');
        }
        return $responseArray;
    }

    function call_service($url, $method)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        print_r($statusCode);
        echo '<br><br>';
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);
        if (isset($error_msg)) {
            echo $statusCode;
            echo '<br>';
            print_r($error_msg);
            notify('แจ้งเตือน', 'API ไม่ตอบสนอง' . $error_msg);
            exit();
        } else {
            return $response;
        }
    }

    $url = 'https://hrws.rajavithi.go.th/mvc/';
    $method = "GET";
    $response = call_service($url, $method);
    echo date("Y/m/d H:i:s", strtotime('+ 5minutes'));
    ?>
    <div id="clock" class="clock"></div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        var dd_date = '<?php echo date("Y/m/d H:i:s", strtotime('+ 5minutes')); ?>';
        $('#clock').countdown(dd_date, {
                elapse: true
            }) /*23:59:59*/
            .on('update.countdown', function(event) {
                if (event.elapsed) {
                    location.reload(true);
                    // $(this).html(event.strftime('เวลาแห่งความสุขของเราได้เริ่มต้นขึ้นแล้ว'));
                } else {
                    // $(this).html(event.strftime('<span style="color:red;">%D วัน %H:%M:%S ชั่วโมง</span>'));
                    $(this).html(event.strftime('<span style="color:red;">%M:%S นาที</span>'));
                }
            });
    });
</script>

</html>