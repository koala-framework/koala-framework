<?php
class Kwc_Articles_Directory_MenuConfig extends Kwf_Component_Abstract_MenuConfig_SameClass
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);

        $acl->add(
            new Kwf_Acl_Resource_ComponentClass_MenuUrl(
                'kwc_article_authors', array('text'=>trlKwfStatic('Authors'), 'icon'=>'user_red'),
                Kwc_Admin::getInstance($this->_class)->getControllerUrl('Authors'),
                $this->_class
            ),
            'settings'
        );
    }
}
