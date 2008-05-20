<?php
class Vpc_News_Category_Directory_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['pool'] = 'Newskategorien'; //todo zu ph, hier engl.
        $ret['childComponentClasses']['detail'] =  'Vpc_News_Category_Detail_Component';
        $ret['ownTreeCache'] = 'Vpc_News_Category_TreeCache';

        //für News-Kategorien Box
        $ret['categoryChildId'] = 'categories';
        $ret['categoryName'] = trlVps('Categories');

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $select = $this->getTreeCacheRow()->getTable()->select()
            ->order('pos')
            ->where('component_class = ?', $this->_getChildComponentClass('detail'))
            ->where('parent_component_id = ?', $this->getComponentId());
        if (!$this->showInvisible()) {
            $select->where('visible = 1');
        }

        $ret['categories'] = $this->getTreeCacheRow()->getTable()->fetchAll($select)->toMenuData();
        return $ret;
    }
    public function getNewsComponent()
    {
        return $this->getTreeCacheRow()
            ->findParentComponent();
    }
}
