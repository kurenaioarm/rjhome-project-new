<?php

namespace RJActivityTime\models;

use Yii;
use yii\base\Model;

class StatuserForm extends Model
{
    public $SelectedDate;

    public $Activity_title;
    public $Description;
    public $Sdate;
    public $Edate;
    public $Bgcolor;
    public $Link;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'Activity_title',
                'Description',
                'Sdate',
                'Edate',
                'Bgcolor',
                'Link',
            ], 'required'],
            [['Statuser','SelectedDate'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'SelectedDate' => 'วันที่',
            'Activity_title' => 'หัวข้อกิจกรรม',
            'Description' => 'รายละเอียดกิจกรรม',
            'Sdate' => 'วันที่เริ่มกิจกรรม',
            'Edate' => 'วันที่สิ้นสุดกิจกรรม',
            'Bgcolor' => 'สีพื้นหลัง',
            'Link' => 'ลิ้งค์ที่เกี่ยวข้อง',
        ];
    }
}