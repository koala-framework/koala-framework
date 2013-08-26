<?php
class Kwc_NewsletterCategory_MenuConfig extends Kwc_Newsletter_MenuConfig
{
    protected function _getMenuConfigText(Kwf_Component_Data $c, $type)
    {
        $ret = parent::_getMenuConfigText($c, $type);
        if ($type == 'categories') {
            return trlKwfStatic('Edit {0}', trlKwfStatic('Categories'));
        }
        return $ret;
    }

    public function addResources(Kwf_Acl $acl)
    {
        $menuConfig = array('icon'=>new Kwf_Asset('package'));

        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        foreach ($components as $c) {
            $menuConfig['text'] = $this->_getMenuConfigText($c, 'categories');
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
                    Kwc_Admin::getInstance($this->_class)->getControllerUrl('Categories').'?componentId='.$c->dbId,
                    $c
                ),
                $this->_getParentResource($acl, 'categories')
            );
        }

        parent::addResources($acl);
    }

    public function getEventsClass()
    {
        return 'Kwf_Component_Abstract_MenuConfig_SameClass_Events';
    }
}
