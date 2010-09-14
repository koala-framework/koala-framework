<?php
class Vpc_List_ChildPages_PageNameOnly_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('List child page names');
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $page = $this->getData()->getPage();
        $ret['childPages'] = $page->getChildPages();
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = array();
        $ret[] = array(
            'model' => 'Vps_Component_Model'
        );
        $ret[] = array(
            'model' => 'Vpc_Root_Category_GeneratorModel'
        );
        $ret[] = array(
            'model' => 'Vpc_Root_Category_Trl_GeneratorModel'
        );
        return $ret;
    }
}
