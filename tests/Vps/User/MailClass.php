<?php
class Vps_User_MailClass extends Vps_Mail_Template
{
    protected $_mailVarsClassName = null;
    public static $mailsSent = 0;

    public function __construct($template, $masterTemplate = 'Master')
    {
        self::$mailsSent = 0;
        parent::__construct($template, $masterTemplate);
    }

    public function send()
    {
        self::$mailsSent++;
    }
}
