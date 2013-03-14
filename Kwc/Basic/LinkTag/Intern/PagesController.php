<?php
class Kwc_Basic_LinkTag_Intern_PagesController extends Kwf_Controller_Action_Component_PagesAbstractController
{
    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }

    public static function getNodeConfig($component)
    {
        $ret = parent::getNodeConfig($component);
        $ret['allowDrag'] = false;
        $ret['allowDrop'] = false;
        return $ret;
    }
}
