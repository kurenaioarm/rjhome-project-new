<?php
        $tns = "(DESCRIPTION =
                  (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.1.90)(PORT = 1521)) )
                  (CONNECT_DATA = (SERVICE_NAME = rjdb) )
              )";
return [
    'components' => [
//        'db' => [
//            'class' => 'yii\db\Connection',
//            'dsn' => '',
//            'username' => '',
//            'password' => '',
//            'charset' => 'utf8',
//        ],

        'oci' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'oci:dbname='.$tns.';charset=UTF8',
            'username' => 's_webintra04',
            'password' => 'RjvT#S04webintra',
        ],

        'oci2' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'oci:dbname='.$tns.';charset=UTF8',
            'username' => 'H_NUTCHANON',
            'password' => '1234',
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
