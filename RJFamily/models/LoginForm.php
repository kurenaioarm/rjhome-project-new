<?php

namespace RJFamily\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $Showpassword = true;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            ['Showpassword', 'boolean'],
            // password is validated by validatePassword()
//            ['password', 'test'],
            [['username'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
            'Showpassword' => 'Show password',
        ];
    }

//    public function test($attribute, $params){
//        $this->addError($attribute, '798798798789798798798798798798798');
//    }

}
