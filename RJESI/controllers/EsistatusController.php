<?php
namespace  RJESI\controllers;

use \yii\web\Controller;
use RJESI\models\StatuserForm;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yii;


class EsistatusController extends Controller
{
    public function actionIndex()
    {
        //=============== การใช้ session=================
        //Yii::$app->session->get('access_token'); //session นำมาใช้
        //Yii::$app->session->set('access_token'); //session เก็บ
        //=========================================

        $StatuserModel = new StatuserForm();
        $API = null;
        $SDateThai = null;
        $EDateThai = null;
        $SUMDayCheck = "0";
        $CountSAME = "0";
        $CountOVER = "0";
        $CountUNDER = "0";


        //============== API ESI Check Token==============
        $API = $this->API_er_esi("", "", "");
        //=========================================

        if($API->json_result == true){
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
                            //========================== API ESI =========================
                            $API = $this->API_er_esi($dateS, $dateE, $StatuserModel->Statuser);
                            foreach ($API->json_data as $dataST){ // Check จำนวน OVER UNDER
                                if($dataST->ST == "OVER"){
                                    $CountOVER++;
                                }else if($dataST->ST == "UNDER"){
                                    $CountUNDER++;
                                }else if($dataST->ST == "SAME"){
                                    $CountSAME++;
                                }
                            }
                            //============================================================
                        }else{
                            $API = null;
                            Yii::$app->session->setFlash('error', 'ไม่สามารถตรวจสอบข้อมูลได้ <b><u>วันที่เลือกย้อนหลังต้องไม่เกิน 30 วัน</u></b> กรุณาระบุวันที่ใหม่อีกครั้ง');
                        }
                    }else{
                        $this->ExcelReport($dateS, $dateE, $StatuserModel->Statuser);
                    }
                }
            }
        }else{
            Yii::$app->session->setFlash('error', 'หมดเวลาการเข้าใช้งาน <br> กรุณา Login ใหม่อีกครั้ง');
            return $this->redirect(['login/login_his']);
        }

        return $this->render('index',[
            'model' => $StatuserModel,
            'SDateThai' => $SDateThai ,
            'EDateThai' => $EDateThai,
            'SUMDayCheck' => $SUMDayCheck,
            'CountSAME' => $CountSAME,
            'CountOVER' => $CountOVER,
            'CountUNDER' => $CountUNDER,
            'API' => $API
        ]);
    }

    public function API_er_esi($SDATE,$EDATE,$EMRGNCY){
        $curl = curl_init();
        $DataToken = 'SDATE='.$SDATE.'&EDATE='.$EDATE.'&EMRGNCY='.$EMRGNCY;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://appintra.rajavithi.go.th/APIRJFamily/er_esi/er_esi',
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

    protected function newPHPexcel($property)
    {
        //คู่มือใช่งาน Spreadsheet แบบ PHP
        //https://phpspreadsheet.readthedocs.io/en/latest/topics/recipes/
        // https://www.htmlgoodies.com/beyond/exploring-phpspreadsheets-formatting-capabilities.html

        $objPHPExcel = new Spreadsheet();
        // Set properties
        $objPHPExcel->getProperties()
            ->setCreator($property['creator'])
            ->setLastModifiedBy($property['lastModifiedBy'])
            ->setTitle($property['title'])
            ->setSubject($property['subject'])
            ->setDescription($property['description'])
            ->setKeywords($property['keywords'])
            ->setCategory($property['category']);

        return $objPHPExcel;
    }

    public function ExcelReport($SDATE,$EDATE,$EMRGNCY)
    {
        ini_set("memory_limit", "300M");
        /////////////////Excel Head///////////////////
        $objPHPExcel = $this->newPHPexcel([
            'creator' => 'Serazu',
            'lastModifiedBy' => 'Serazu',
            'title' => 'รายงานESI',
            'subject' => 'รายงานESI',
            'description' => 'รายงานESI',
            'keywords' => 'pdf php',
            'category' => 'Serazu Report',
        ]);

        //header
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'HN')
            ->setCellValue('B1', 'วันส่งตรวจ')
            ->setCellValue('C1', 'ชื่อคนไข้')
            ->setCellValue('D1', 'เวลาซักประวัติ')
            ->setCellValue('E1', 'สถานะสุดท้าย')
            ->setCellValue('F1', 'Trauma')
            ->setCellValue('G1', 'ESI DOC')
            ->setCellValue('H1', 'ชื่อ แพทย์')
            ->setCellValue('I1', 'ESI')
            ->setCellValue('J1', 'Chief Complaint')
            ->setCellValue('K1', 'Status')
            ->setCellValue('L1', 'BP')
            ->setCellValue('M1', 'BP')
            ->setCellValue('N1', 'T')
            ->setCellValue('O1', 'P')
            ->setCellValue('P1', 'R');

        //============================ ใส่ Color ให้ตราง  ===============================
        $objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ff5bccc8');
        //============================ ใส่ Border ให้ตราง  ==============================
        $objPHPExcel->getActiveSheet()->getStyle('K1')->getBorders()
            ->getOutline()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getBorders()
            ->getOutline()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        //========================================================================

        /////////////////Excel Head///////////////////
        //=================== API ESI ================
        $API =  $this->API_er_esi($SDATE, $EDATE, $EMRGNCY);
        //=========================================

        $count = 2;
        ///////Begin Data Loop///////////////////
        //var_dump($API->json_data);
        $esi_datas = $API->json_data;
        foreach ($esi_datas as $data) {

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $count, $data->HN)
                ->setCellValue('B' . $count, $data->DDATE." ".$data->DTIME)
                ->setCellValue('C' . $count, $data->PNAME)
                ->setCellValue('D' . $count, $data->HDATE." ".$data->HTIME)
                ->setCellValue('E' . $count, $data->FSTATUS)
                ->setCellValue('F' . $count, $data->TRAUMA)
                ->setCellValue('G' . $count, $data->ESI_DOC)
                ->setCellValue('H' . $count, $data->MEDICO)
                ->setCellValue('I' . $count, $data->ESI)
                ->setCellValue('J' . $count, $data->CHIEFCOMPLAINT)
                ->setCellValue('K' . $count, $data->ST)
                ->setCellValue('L' . $count, $data->HBPN)
                ->setCellValue('M' . $count, $data->LBPN)
                ->setCellValue('N' . $count, $data->BT)
                ->setCellValue('O' . $count, $data->PR)
                ->setCellValue('P' . $count, $data->RR);

            //========== ใส่ Color ให้ตราง  https://ankiewicz.com/color/picker/ff0000 =============
            if($data->ST == "OVER"){
                $objPHPExcel->getActiveSheet()->getStyle('K' . $count)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ff9d90dc');
            }else if($data->ST == "UNDER"){
                $objPHPExcel->getActiveSheet()->getStyle('K' . $count)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('fff3969a');
            }else if($data->ST == "SAME"){
                $objPHPExcel->getActiveSheet()->getStyle('K' . $count)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ff5bccc8');
            }
            //========================================================================
            $count++;
        }
        //============================ ใส่ Border ให้ตราง   =================================
        $BorderCount = $count-1;
        $objPHPExcel->getActiveSheet()->getStyle('K2:'.'K'. $BorderCount)->getBorders()
            ->getOutline()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.'P'. $BorderCount)->getBorders()
            ->getOutline()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        //===========================================================================
        /////////////////Begin Data Loop///////////////////


        /////////////////Excel Foot///////////////////
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('ESI SCREENING');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        self::setHttpHeaders('Report ER SCREENING');

        $objWriter = IOFactory::createWriter($objPHPExcel, 'Xls');
        $objWriter->save('php://output');
        exit();
//        return 1;
//        /////////////////Excel Foot///////////////////
    }

    protected function setHttpHeaders($filename)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
    }

}