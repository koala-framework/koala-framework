<?php
class Kwc_NewsletterCategory_Admin extends Kwc_Newsletter_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        if (!$acl->has('kwc_newsletter')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_newsletter',
                array('text'=>trlKwf('Newsletter'), 'icon'=>'email_open_image.png')), 'kwf_component_root');
        }

        $menuConfig = array('icon'=>new Kwf_Asset('package'));

        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        foreach ($components as $c) {
            $menuConfig['text'] = trlKwf('Edit {0}', trlKwf('Categories'));
            if (count($components) > 1) {
                $subRoot = $c;
                while($subRoot = $subRoot->parent) {
                    if (Kwc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
                }
                if ($subRoot) {
                    $menuConfig['text'] .= ' ('.$subRoot->name.')';
                }
            }
            $acl->add(
                new Kwf_Acl_Resource_Component_MenuUrl(
                    'kwc_'.$c->dbId.'-categories',
                    $menuConfig,
                    $this->getControllerUrl('Categories').'?componentId='.$c->dbId,
                    $c
                ),
                'kwc_newsletter'
            );
        }
        parent::addResources($acl);
    }
}
