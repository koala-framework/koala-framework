<?php
class Kwc_User_Edit_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_User_Edit_Form_Component';
        $ret['forms'] = 'all';
        $ret['cssClass'] = 'webStandard';
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['authedUser'] = Kwf_Registry::get('userModel')->getAuthedUser();
        $ret['userProfile'] = false;
        if ($ret['authedUser']) {
            $ret['userProfile'] = Kwf_Component_Data_Root::getInstance()
                ->getComponentByClass(
                    'Kwc_User_Directory_Component',
                    array('subroot' => $this->getData())
                )
                ->getChildComponent('_' .  $ret['authedUser']->id);
        }
        return $ret;
    }
}
