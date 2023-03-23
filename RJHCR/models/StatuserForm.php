<?php

namespace RJHCR\models;

use Yii;
use yii\base\Model;

class StatuserForm extends Model
{
    public $Statuser;
    public $SelectedDate;
    public $LrfrlctType;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Statuser','SelectedDate'], 'required'],
            [['Statuser','SelectedDate'], 'safe'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'Statuser' => 'สถานะ',
            'SelectedDate' => 'วันที่',
            'LrfrlctType' => 'หน่วยงาน',
        ];
    }
}