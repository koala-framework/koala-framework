<?php
abstract class Vps_Component_Generator_Plugin_StatusUpdate_Backend_Abstract
{
    protected $_type = null;
    private $_row = null;

    abstract public function getAuthUrl();
    abstract public function processCallback($queryData);
    abstract public function getName();
    abstract public function send($message, $logRow);

    public function __construct($callbackUrl)
    {
    }

    protected final function _getAuthRow()
    {
        if (!$this->_row) {
            $m = Vps_Model_Abstract::getInstance('Vps_Component_Generator_Plugin_StatusUpdate_AuthModel');

            $s = new Vps_Model_Select();
            $s->whereEquals('type', $this->_type);
            $ret = $m->getRow($s);
            if (!$ret) {
                $ret = $m->createRow();
                $ret->type = $this->_type;
            }
            $this->_row = $ret;
        }
        return $this->_row;
    }

    public final function isAuthed()
    {
        return !!$this->_getAuthRow()->auth_token;
    }
}
