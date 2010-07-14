<?php
class Vpc_NewsletterCategory_Admin extends Vpc_Newsletter_Admin
    implements Vpc_Directories_Item_Directory_PluginAdminInterface
{
    public function addResources(Vps_Acl $acl)
    {
        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $c = $components[0];
        $icon = new Vps_Asset('package');

        $acl->add(
            new Vps_Acl_Resource_ComponentClass_MenuUrl(
                $c->componentClass,
                array('text'=>trlVps('Edit {0}', trlVps('Categories')), 'icon'=>$icon),
                $this->getControllerUrl('Categories').'?componentId='.$c->dbId
            ),
            'vpc_newsletter'
        );
        parent::addResources($acl);
    }

    public function getPluginExtConfig()
    {
        $ret = array();
        $ret['pluginClass'] = 'Vpc.NewsletterCategory.Plugin';
        $ret['controllerUrl'] = $this->getControllerUrl('SubscribeCategories');
        return $ret;
    }

    protected function _getPluginAdmins()
    {
        return array($this);
    }
}
