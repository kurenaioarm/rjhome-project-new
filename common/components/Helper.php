<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;

class Helper extends Component
{

    private static $thaiDay = array("",
        "จ.", "อัง.", "พ.",
        "พฤ.", "ศ.", "ส.",
        "อา.");
    private static $thaiDay_full = array("",
        "จันทร์", "อังคาร", "พุธ",
        "พฤหัสบดี", "ศุกร์", "เสาร์",
        "อาทิตย์");
    private static $thaiMonth = array("",
        "ม.ค.", "ก.พ.", "มี.ค.",
        "เม.ย.", "พ.ค.", "มิ.ย.",
        "ก.ค.", "ส.ค.", "ก.ย",
        "ต.ค.", "พ.ย.", "ธ.ค");
    private static $thaiMonth_full = array("",
        "มกราคม", "กุมภาพันธ์", "มีนาคม",
        "เมษายน", "พฤษภาคม", "มิถุนายน",
        "กรกฎาคม", "สิงหาคม", "กันยายน",
        "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
    private static $thaiNumber = array("๐", "๑", "๒", "๓", "๔", "๕", "๖", "๗", "๘", "๙");

    public static function thaiDate($formats,
                                    $time_string = NULL,
                                    $enable_thaiNumber = false,
                                    $buddhist_era = true)
    {
        // $formats is same as format of PHP date();
        $output = "";
        $datetime = new \DateTime($time_string);
        $format_char = str_split($formats);
        foreach ($format_char as $format) {
            switch ($format) {
                case "D":
                    $text = self::$thaiDay[(int)$datetime->format('N')];
                    break;
                case "l":
                    $text = self::$thaiDay_full[(int)$datetime->format('N')];
                    break;
                case "S":
                    $text = $datetime->format('N');
                    break;
                case "F":
                    $text = self::$thaiMonth_full[(int)$datetime->format('n')];
                    break;
                case "M":
                    $text = self::$thaiMonth[(int)$datetime->format('n')];
                    break;
                case "o":
                case "Y":
                    if ($buddhist_era) {
                        $text = $datetime->format('o') + 543;
                    } else {
                        $text = $datetime->format('o');
                    }
                    break;
                case "y":
                    if ($buddhist_era) {
                        $text = $datetime->format('y') + 43;
                        $text = substr($text, -2);
                    } else {
                        $text = $datetime->format('y');
                    }
                    break;
                case "`":
                    $text = '<br/>';
                    break;
                default:
                    $text = $datetime->format($format);
            }
            if ($enable_thaiNumber) {
                $output .= self::to_thainumber($text);
            } else {
                $output .= $text;
            }
        }
        return $output;
    }


    public function dateThaiFull($date)
    {
        if ($date != null)
            return self::thaiDate('j F พ.ศ. Y', $date);
        else
            return null;
    }

    public function dateThaiTwoDigit($date)
    {
        if ($date != null)
            return self::thaiDate('d/m/y', $date);
        else
            return null;
    }

    public function dateTimeThaiTwoDigit($datetime)
    {
        if ($datetime != null)
            return self::thaiDate('d/m/y H:i', $datetime);
        else
            return null;
    }

    public function duration($begin, $end)
    {
        $remain = intval(strtotime($end) - strtotime($begin));
        $wan = floor($remain / 86400);
        $l_wan = $remain % 86400;
        $hour = floor($l_wan / 3600);
        $l_hour = $l_wan % 3600;
        $minute = floor($l_hour / 60);
        $second = $l_hour % 60;
        if ($wan >= 365) {//ผ่านมาเเล้วเกินปี
            $year = round($wan / 365);
            return "ผ่านมาแล้ว " . $year . " ปี";
        } elseif ($wan >= 1) {//ผ่านมาเเล้วเกินวัน
            return "ผ่านมาแล้ว " . $wan . " วัน " . $hour . " ชั่วโมง ";
        } elseif ($hour >= 1) {//ผ่านมาเเล้วเกินช.ม.
            return "ผ่านมาแล้ว " . $hour . " ชั่วโมง " . $minute . " นาที ";
        } elseif ($minute >= 1) {//ผ่านมาเเล้วเกินนาที
            return "ผ่านมาแล้ว " . $minute . " นาที " . $second . " วินาที";
        } else {
            return "ผ่านมาแล้ว " . $second . " วินาที";
        }
    }

    public function currencyTwoDecimal($number)
    {
        $result = null;

        if ($number != null) {
            if (strpos($number, ".") !== false) {
                $result = number_format($number, 2, '.', ',');
            } else {
                $result = number_format($number, 0, '.', ',');
            }
        } else {
            $result = 0;
        }

        return $result;
    }

    public function convertCurrencyToText($amountNumber)
    {
        $amountNumber = number_format($amountNumber, 2, ".", "");
        $pt = strpos($amountNumber, ".");
        $number = $fraction = "";
        if ($pt === false)
            $number = $amountNumber;
        else {
            $number = substr($amountNumber, 0, $pt);
            $fraction = substr($amountNumber, $pt + 1);
        }

        $result = "";
        $baht = $this->readNumber($number);
        if ($baht != "")
            $result .= $baht . "บาท";

        $decimal = $this->readNumber($fraction);
        if ($decimal != "")
            $result .= $decimal . "สตางค์";
        else
            $result .= "ถ้วน";
        return $result;
    }

    public function readNumber($number)
    {
        $positionCall = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
        $numberCall = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
        $number = $number + 0;
        $result = "";
        if ($number == 0) return $result;
        if ($number > 1000000) {
            $result .= $this->readNumber(intval($number / 1000000)) . "ล้าน";
            $number = intval(fmod($number, 1000000));
        }

        $divider = 100000;
        $pos = 0;
        while ($number > 0) {
            $countNumber = intval($number / $divider);
            $result .= (($divider == 10) && ($countNumber == 2)) ? "ยี่" :
                ((($divider == 10) && ($countNumber == 1)) ? "" :
                    ((($divider == 1) && ($countNumber == 1) && ($result != "")) ? "เอ็ด" : $numberCall[$countNumber]));
            $result .= ($countNumber ? $positionCall[$pos] : "");
            $number = $number % $divider;
            $divider = $divider / 10;
            $pos++;
        }
        return $result;
    }

    public function isWeekend($date)
    {
        return (date('N', strtotime($date)) >= 6);
    }

    public function dateIsBetween($date)
    {
        return ((date('H', strtotime($date)) >= 9) && (date('H', strtotime($date)) < 18));
    }

    public function dateInMonth($date)
    {
        $year = date('Y', strtotime($date));
        $month = date('n', strtotime($date));
        $countDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        return $countDay;
    }



    public function dateDivTest($t1, $t2)
    { // ส่งวันที่ที่ต้องการเปรียบเทียบ ในรูปแบบ มาตรฐาน 2006-03-27 21:39:12

        $t1Arr = $this->splitTime($t1);
        $t2Arr = $this->splitTime($t2);

        $Time1 = mktime($t1Arr["h"], $t1Arr["m"], $t1Arr["s"], $t1Arr["M"], $t1Arr["D"], $t1Arr["Y"]);
        $Time2 = mktime($t2Arr["h"], $t2Arr["m"], $t2Arr["s"], $t2Arr["M"], $t2Arr["D"], $t2Arr["Y"]);
        $TimeDiv = abs($Time2 - $Time1);

        $Time["D"] = intval($TimeDiv / 86400); // จำนวนวัน
        $Time["H"] = intval(($TimeDiv % 86400) / 3600); // จำนวน ชั่วโมง
        $Time["M"] = intval((($TimeDiv % 86400) % 3600) / 60); // จำนวน นาที
        $Time["S"] = intval(((($TimeDiv % 86400) % 3600) % 60)); // จำนวน วินาที

        $minutes = (($Time["D"] * 24) * 60) + ($Time["H"] * 60) + $Time["M"];

        return $minutes;
    }

    public function splitTime($time)
    { // เวลาในรูปแบบ มาตรฐาน 2006-03-27 21:39:12
        $timeArr["Y"] = substr($time, 2, 2);
        $timeArr["M"] = substr($time, 5, 2);
        $timeArr["D"] = substr($time, 8, 2);
        $timeArr["h"] = substr($time, 11, 2);
        $timeArr["m"] = substr($time, 14, 2);
        $timeArr["s"] = substr($time, 17, 2);
        return $timeArr;
    }



    //จัดการ array 2มิติจัดเรียง มากไปน้อย /น้อยไปมาก
    public function orderArrayNum($array, $key, $order = "ASC")
    {
        $tmp = array();
        foreach ($array as $akey => $array2) {
            $tmp[$akey] = $array2[$key];
        }

        if ($order == "DESC") {
            arsort($tmp);
        } else {
            asort($tmp);
        }

        $tmp2 = array();
        foreach ($tmp as $key => $value) {
            $tmp2[$key] = $array[$key];
        }

        return $tmp2;
    }

}
