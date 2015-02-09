<?php
class Kwf_Component_Plugin_Inherit_Test2_Plugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewReplace, Kwf_Component_Plugin_Interface_Login
{
    public function replaceOutput($renderer)
    {
        return 'replace';
    }
    public function isLoggedIn()
    {
        return false;
    }
}
