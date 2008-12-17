<?php
class Vpc_User_Edit_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_User_Edit_Form_Component';
        $ret['forms'] = 'all';
        $ret['cssClass'] = 'webStandard';
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['authedUser'] = Vps_Registry::get('userModel')->getAuthedUser();
        $ret['userProfile'] = false;
        if ($ret['authedUser']) {
            $ret['userProfile'] = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass(
                    'Vpc_User_Directory_Component',
                    array('subroot' => $this->getData())
                )
                ->getChildComponent('_' .  $ret['authedUser']->id);
        }
        return $ret;
    }
}
