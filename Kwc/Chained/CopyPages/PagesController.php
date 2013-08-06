<?php
class Kwc_Chained_CopyPages_PagesController extends Kwf_Controller_Action_Component_PagesAbstractController
{
    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }
}
