<?php
class Vpc_Directories_CategoryTree_Detail_Breadcrumbs_Component
    extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['currentCategories'] = trlVps('Current category:');
        $ret['placeholder']['categoryTreeRootText'] = null;
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $detail = $this->getData()->parent;
        $directory = $detail->parent;

        $breadcrumbs = array($detail);

        $row = $detail->row->getRow()->findParentRow($detail->row->getModel()->getTable());
        while ($row) {
            $breadcrumbs[] = $directory->getChildComponent('_'.$row->id);
            $row = $row->findParentRow($row->getTable());
        }
        $ret['root'] = $this->_getCategoryTreeRoot();
        $ret['breadcrumbs'] = array_reverse($breadcrumbs);
        return $ret;
    }

    protected function _getCategoryTreeRoot()
    {
        return null;
    }
}
