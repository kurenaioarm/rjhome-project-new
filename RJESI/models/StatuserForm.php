<?php

namespace RJESI\models;

use Yii;
use yii\base\Model;

class StatuserForm extends Model
{
    public $Statuser;
    public $SelectedDate;

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
        ];
    }
}