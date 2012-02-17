<?php
class Kwf_View_Mail extends Kwf_View implements Kwf_View_MailInterface
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
