<?php
abstract class Vpc_News_Detail_Abstract_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Vpc_News_Detail_Paragraphs_Component';
        $ret['hasModifyNewsData'] = true;
        return $ret;
    }
    public static function modifyNewsData(Vps_Component_Data $new)
    {
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        //todo: 404 wenn news abgelaufen
        return $return;
    }

    public function getNewsComponent()
    {
        return $this->getData()->findParentComponent();
    }
}
