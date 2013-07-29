<?php
class Kwc_Tags_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        $acl->add(new Kwf_Acl_Resource_ComponentClass_MenuUrl(
            'kwc_tags',
            array(
                'text' => Kwf_Trl::getInstance()->trlStaticExecute($this->_getSetting('componentName')),
                'icon' => 'tag_blue_edit'
            ),
            Kwc_Admin::getInstance($this->_class)->getControllerUrl('Grid'),
            $this->_class
        ), 'settings');
    }
}
