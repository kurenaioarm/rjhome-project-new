<?php

namespace RJDonate\models;

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
    //------------------ ลงทะเบียน Admin ---------------------
    public  $Permission_level;
    public  $Permission_level2;
    public $Idcard;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password','Permission_level','Permission_level2','Idcard'], 'required'],
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
            //------------------ ลงทะเบียน Admin ---------------------
            'Permission_level'  => 'ระดับสิทธิ์',
            'Permission_level2'  => 'ระดับสิทธิ์',
            'Idcard'  => 'ระบุ : เลขบัตรประชาชน',
        ];
    }

//    public function test($attribute, $params){
//        $this->addError($attribute, '798798798789798798798798798798798');
//    }

}
