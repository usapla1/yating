<?php
return array(
    'DEFAULT_MODULE'            => 'Index', //默认模块
    'URL_MODEL'                 => '3', //URL模式
    'SESSION_AUTO_START'        => true, //是否开启session
    'APP_AUTOLOAD_REG'          => true,
    'LOG_SQL'                   => true,
    'LOG_PATH'                  => "E:/logs/yun/",
    'UPLOAD_PATH'               => "E:/www/upload.coobar.cn/",
    'IS_TEST'                   => true,
    'CUSTOM'                    => 0,

    'DB_TYPE'                   => 'mysqli',
    'DB_HOST'                   => '127.0.0.1',
    'DB_PORT'                   => 19306,
    'DB_NAME'                   => 'xiaoxia',
    'DB_USER'                   => 'root',
    'DB_PWD'                    => 'j33mqXT5ARXXuP5w',
    'DB_PREFIX'                 => 'cb_',
    'DB_DEBUG'                  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增

    // 配置邮件发送服务器 建议163邮箱
    'MAIL_HOST' =>'smtp.163.com',//smtp服务器的名称
    'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
    'MAIL_USERNAME' =>'',//你的邮箱名
    'MAIL_FROM' =>'',//发件人地址
    'MAIL_FROMNAME'=>'李玉鑫',//发件人姓名
    'MAIL_PASSWORD' =>'',//邮箱密码
    'MAIL_CHARSET' =>'utf-8',//设置邮件编码
    'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件


);