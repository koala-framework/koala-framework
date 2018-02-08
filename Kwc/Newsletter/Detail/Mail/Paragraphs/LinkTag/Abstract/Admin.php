<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Abstract_Admin
    extends Kwc_Basic_LinkTag_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return Kwc_Abstract::getSetting($this->_class, 'componentName');
    }
}
