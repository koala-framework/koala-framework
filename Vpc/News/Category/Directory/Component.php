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

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Category/Directory/Plugin.js';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $childComponents = $this->getData()->getChildComponents(array('treecache' => 'Vpc_News_Category_Directory_TreeCache'));

        $ret['categories'] = $childComponents;
        return $ret;
    }
    public function getNewsComponent()
    {
        return $this->getData()->findParentComponent();
    }
}
