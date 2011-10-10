<?php
class Vpc_NewsletterCategory_Admin extends Vpc_Newsletter_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        if (!$acl->has('vpc_newsletter')) {
            $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_newsletter',
                array('text'=>trlVps('Newsletter'), 'icon'=>'email_open_image.png')), 'vps_component_root');
        }

        $menuConfig = array('icon'=>new Vps_Asset('package'));

        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        foreach ($components as $c) {
            $menuConfig['text'] = trlVps('Edit {0}', trlVps('Categories'));
            if (count($components) > 1) {
                $subRoot = $c;
                while($subRoot = $subRoot->parent) {
                    if (Vpc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
                }
                if ($subRoot) {
                    $menuConfig['text'] .= ' ('.$subRoot->name.')';
                }
            }
            $acl->add(
                new Vps_Acl_Resource_Component_MenuUrl(
                    'vpc_'.$c->dbId.'-categories',
                    $menuConfig,
                    $this->getControllerUrl('Categories').'?componentId='.$c->dbId,
                    $c
                ),
                'vpc_newsletter'
            );
        }
        parent::addResources($acl);
    }
}
