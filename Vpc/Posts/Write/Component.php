<?php
class Vpc_Posts_Write_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Posts_Write_Form_Component';
        $ret['generators']['child']['component']['lastPosts'] = 'Vpc_Posts_Write_LastPosts_Component';
        $ret['flags']['noIndex'] = true;
        $ret['plugins'] = array('Vps_Component_Plugin_Login_Component');
        $ret['viewCache'] = false; //wegen isSaved
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['isSaved'] = $this->getData()->getChildComponent('-form')->getComponent()->isSaved();
        return $ret;
    }

}
