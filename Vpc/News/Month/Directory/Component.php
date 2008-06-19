<?php
class Vpc_News_Month_Directory_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['detail'] =  'Vpc_News_Month_Detail_Component';
        $ret['ownTreeCache'] = 'Vpc_News_Month_TreeCache';

        //für News-Kategorien Box
        $ret['categoryChildId'] = 'months';
        $ret['categoryName'] = trlVps('Months');

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
/*
        $select = $this->getData()->getTable()->select()
            ->order('pos')
            ->where('component_class = ?', $this->_getChildComponentClass('detail'))
            ->where('parent_component_id = ?', $this->getComponentId());
        if (!$this->showInvisible()) {
            $select->where('visible = 1');
        }

        $ret['months'] = $this->getTreeCacheRow()->getTable()->fetchAll($select)->toMenuData();
*/
        $ret['months'] = array(); // TODO
        return $ret;
    }
    public function getNewsComponent()
    {
        return $this->getData()->getParentComponent();
    }
}
