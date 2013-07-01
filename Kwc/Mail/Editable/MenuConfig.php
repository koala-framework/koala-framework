<?php
class Kwc_Mail_Editable_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        if (!$acl->has('kwc_Kwc_Mail_Editable')) {
            $acl->add(new Kwc_Mail_Editable_AdminResource($this->_class,
                    array('text'=>trlKwf('Mail Texts'), 'icon'=>'email_open.png'),
                    Kwc_Admin::getInstance($this->_class)->getControllerUrl('Mails')), 'kwf_component_root');
        }
    }
}
