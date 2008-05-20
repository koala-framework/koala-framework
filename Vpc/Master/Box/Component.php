<?php
class Vpc_Master_Box_Component extends Vpc_Master_Abstract
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
            $where = array(
                'box = ?' => $id,
                'tree_url = ?' => $this->getTreeCacheRow()->tree_url
            );
            $row = $this->getTreeCacheRow()->getTable()->fetchAll($where)->current();
            $vars['boxes'][$id] = $row->component_id;
        }
        return $vars;
    }
}
