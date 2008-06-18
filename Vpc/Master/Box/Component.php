<?php
class Vpc_Master_Box_Component extends Vpc_Master_Abstract
{   
    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        $vars['boxes'] = array();
        foreach ($this->_getSetting('childComponentClasses') as $id => $boxComponentClass) {
            $where = array(
                'box = ?' => $id,
                'tree_url = ?' => $this->getTreeCacheRow()->tree_url
            );
            $row = $this->getTreeCacheRow()->getTable()->fetchAll($where)->current();
            if ($row) {
                $vars['boxes'][$id] = $row->component_id;
            }
        }
        return $vars;
    }
}
