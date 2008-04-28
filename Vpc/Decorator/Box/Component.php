<?php
class Vpc_Decorator_Box_Component extends Vpc_Decorator_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'boxComponentClasses'   => array()
        ));
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        foreach ($this->_getSetting('boxComponentClasses') as $id => $boxComponentClass) {
            $componentId = $this->getTreeCacheRow()->component_id . '-box' . $id;
            $row = $this->getTreeCacheRow()->getTable()->find($componentId)->current();
            if (!$row) {
                $componentId = $this->getTreeCacheRow()->component_id . '-' . $id;
            }
            $vars['boxes'][$id] = $componentId; 
        }
        return $vars;
    }
}
