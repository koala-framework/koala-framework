<?php
class Kwc_Articles_Category_Directory_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        $acl->add(
            new Kwf_Acl_Resource_ComponentClass_MenuUrl(
                'kwc_article_category', array('text'=>trlKwfStatic('Categories'), 'icon'=>'application_side_tree'),
                Kwc_Admin::getInstance($this->_class)->getControllerUrl('Categories'),
                $this->_class
            ),
            'settings'
        );
    }
}
