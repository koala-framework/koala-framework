<?php
class Kwc_Basic_LinkTag_Intern_PagesController extends Kwf_Controller_Action_Component_PagesAbstractController
{
    protected $_modelName = 'Kwc_Basic_LinkTag_Intern_PagesModel';

    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }
}
