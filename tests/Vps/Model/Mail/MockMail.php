<?php

class Vps_Model_Mail_MockMail extends Vps_Mail_Template
{
    public static $sendCalled = 0;
    public static $addToCalled = array();
    public static $setFromCalled = array();
    public static $returnPathCalled = null;
    public static $addCcCalled = array();
    public static $addBccCalled = array();
    public static $addHeaderCalled = array();
    public static $data = array();

    static public function resetCalls()
    {
        self::$addToCalled = array();
        self::$setFromCalled = array();
        self::$sendCalled = 0;
        self::$returnPathCalled = null;
        self::$addCcCalled = array();
        self::$addBccCalled = array();
        self::$addHeaderCalled = array();
        self::$data = array();
    }

    public function __set($var, $val)
    {
        self::$data[$var] = $val;
        parent::__set($var, $val);
    }

    public function send()
    {
        self::$sendCalled++;
    }

    public function addTo($email, $name='')
    {
        self::$addToCalled[] = array($email, $name);
        return parent::addTo($email, $name);
    }

    public function setFrom($email, $name='')
    {
        self::$setFromCalled = array($email, $name);
        return parent::setFrom($email, $name);
    }

    public function setReturnPath($email)
    {
        self::$returnPathCalled = $email;
        return parent::setReturnPath($email);
    }

    public function addCc($email, $name = '')
    {
        self::$addCcCalled[] = array($email, $name);
        return parent::addCc($email, $name);
    }

    public function addHeader($name, $value, $append = false)
    {
        self::$addHeaderCalled[] = array($name, $value, $append);
        return parent::addHeader($name, $value, $append);
    }

    public function addBcc($email)
    {
        self::$addBccCalled[] = $email;
        return parent::addBcc($email);
    }

}