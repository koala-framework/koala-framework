<?php
abstract class Vpc_News_Detail_Abstract_Component extends Vpc_Abstract_Composite_Component
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
        return $return;
    }

    public function getNewsComponent()
    {
        return $this->getTreeCacheRow()
            ->findParentComponent();
    }
}
