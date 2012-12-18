<?php
class Kwf_View_Mail extends Kwf_View
{
    protected $_masterTemplate = null;

    public function setMasterTemplate($tpl)
    {
        $this->_masterTemplate = $tpl;
    }

    public function getMasterTemplate()
    {
        return $this->_masterTemplate;
    }
}
