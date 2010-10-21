<?php
class Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Admin
    extends Vpc_Basic_LinkTag_Abstract_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        return Vpc_Abstract::getSetting($this->_class, 'componentName');
    }
}
