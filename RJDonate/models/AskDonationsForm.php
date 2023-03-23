<?php
namespace RJDonate\models;

use Yii;
use yii\base\Model;

class AskDonationsForm extends Model
{
    public $Donate_Id;
    public $Report_Date;
    public $Report_Time;
    public $Name_Type;
    public $Letter_Type;
    public $Register_Type;
    //----------------------- ข้อมูลส่วนตัว ------------------------------
    public $My_Pname;
    public $My_Name;
    public $My_Lname;
    public $My_Company;
    public $Taxpayer_Number;
    public  $Telephone;
    public  $Telephone_Staff;
    public  $Donate_Note;
    //------------------------ ข้อมูลที่อยู่ -------------------------------
    public $Address_No;
    public $Alley;
    public $Road;
    public $Tambon;
    public $District;
    public $Province;
    public $Zip_Code;
    //------------------------ ข้อมูลขอบริจาคสิ่งของ -------------------------------
    public $Itemtype;
    public $Itemmaster;
    public $Othername;
    public $Quantity;
    public $Itemcu;
    public $Price;
    public $Total_Price;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'Name_Type',
                'Letter_Type',
                'My_Pname',
                'My_Name',
                'My_Lname',
            ], 'required']
        ];
    }

    public function attributeLabels()
    {
        return [

            'Report_Date' => 'วันที่ทำรายการ',
            'Report_Time' => 'เวลาที่ทำรายการ',
            'Name_Type'  => 'ประเภทบุคคล',
            'Letter_Type'  => 'ประเภทการรับหนังสือ',
            'Register_Type'  => 'ประเภทการ ลงทะเบียน',
            //------------- ข้อมูลส่วนตัว -----------------
            'My_Pname' => 'คำนำหน้าชื่อ',
            'My_Name' => 'ชื่อ',
            'My_Lname'  => 'นามสกุล',
            'My_Company'  => 'บริษัท/นิติบุคล/มูลนิธิ',
            'Taxpayer_Number' => 'เลขผู้เสียภาษี',
            'Telephone'  => 'เบอร์โทรศัพท์',
            'Telephone_Staff'  => 'เบอร์โทรศัพท์พนักงาน',
            'Donate_Note'   => 'หมายเหตุ บริจาคเนื่องในโอกาส',
            //-------------- ข้อมูลที่อยู่ -------------------
            'Address_No' => 'ที่อยู่ เลขที่',
            'Alley' => 'ซอย',
            'Road' => 'ถนน',
            'Tambon' => 'ตำบล/แขวง',
            'District' => 'อำเภอ/เขต',
            'Province' => 'จังหวัด',
            'Zip_Code' => 'รหัสไปรษณีย์',
              //---------- ข้อมูลขอบริจาคสิ่งของ -------------
            'Itemtype' => 'ประเภทสิ่งของบริจาค',
            'Itemmaster' => 'สิ่งของบริจาค',
            'Othername' => 'ระบุชื่อ',
            'Quantity' => 'จำนวน',
            'Itemcu' => 'หน่วยนับ',
            'Price' => 'มูลค่า(ต่อหน่วย)',
            'Total_Price' => 'มูลค่าทั้งหมด',
        ];
    }
}