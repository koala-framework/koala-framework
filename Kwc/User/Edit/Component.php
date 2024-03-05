<?php
class Kwc_User_Edit_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['form'] = 'Kwc_User_Edit_Form_Component';
        $ret['forms'] = 'all';
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['viewCache'] = false;
        $ret['componentName'] = trlKwfStatic('Userprofile');
        $ret['flags']['noIndex'] = true;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['authedUser'] = Kwf_Registry::get('userModel')->getAuthedUser();
        $ret['userProfile'] = false;
        if ($ret['authedUser']) {
            $root = Kwf_Component_Data_Root::getInstance();
            if ($root->getComponentByClass('Kwc_User_Directory_Component', array('subroot' => $this->getData()))) {
                $ret['userProfile'] = $root->getComponentByClass(
                    'Kwc_User_Directory_Component',
                    array('subroot' => $this->getData())
                )
                ->getChildComponent('_' .  $ret['authedUser']->id);
            }
        }
        return $ret;
    }
}
