<?php
class Kwc_Directories_CategoryTree_Detail_Breadcrumbs_Component
    extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['currentCategories'] = trlKwfStatic('Current category:');
        $ret['placeholder']['categoryTreeRootText'] = null;
        $ret['rootElementClass'] = 'kwfup-webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $detail = $this->getData()->parent;
        $directory = $detail->parent;

        $breadcrumbs = array();

        foreach ($detail->row->getTreePathRows() as $row) {
            $breadcrumbs[] = $directory->getChildComponent('_'.$row->id);
        }
        $ret['root'] = $this->_getCategoryTreeRoot();
        $ret['breadcrumbs'] = $breadcrumbs;
        return $ret;
    }

    protected function _getCategoryTreeRoot()
    {
        return null;
    }
}
