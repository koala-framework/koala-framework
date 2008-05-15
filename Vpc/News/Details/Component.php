<?php
class Vpc_News_Details_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['content'] = 'Vpc_Paragraphs_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $id = $this->getTreeCacheRow()->component_id;
        $return['content'] = $id.'-content';
        return $return;
    }
}
