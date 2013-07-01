<?php
class Kwc_Editable_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        if (!$acl->has('kwc_Kwc_Editable')) {
            $acl->add(new Kwc_Editable_AdminResource($this->_class,
                    array('text'=>trlKwf('Texts'), 'icon'=>'page_white_text.png'),
                    Kwc_Admin::getInstance($this->_class)->getControllerUrl('Components')), 'kwf_component_root');
        }
    }
}
