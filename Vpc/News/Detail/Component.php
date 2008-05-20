<?php
class Vpc_News_Detail_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['content'] = 'Vpc_News_Detail_Paragraphs_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        //todo: 404 wenn news abgelaufen
        $id = $this->getTreeCacheRow()->component_id;
        $return['content'] = $id.'-content';
        return $return;
    }

    public function getNewsComponent()
    {
        return $this->getTreeCacheRow()
            ->findParentComponent();
    }
}
