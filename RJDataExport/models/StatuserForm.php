<?php

namespace RJDataExport\models;

use Yii;
use yii\base\Model;

class StatuserForm extends Model
{
    public $SelectedDate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Statuser','SelectedDate'], 'safe'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'SelectedDate' => 'วันที่',
        ];
    }
}