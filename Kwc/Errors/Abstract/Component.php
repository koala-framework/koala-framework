<?php
abstract class Kwc_Errors_Abstract_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['contentSender'] = 'Kwc_Errors_ContentSender';
        return $ret;
    }

    protected $_exception;
    public function setException($e)
    {
        $this->_exception = $e;
    }

    public function getException()
    {
        return $this->_exception;
    }
}
