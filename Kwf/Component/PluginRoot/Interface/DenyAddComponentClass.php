<?php
interface Kwf_Component_PluginRoot_Interface_DenyAddComponentClass
{
    public function isComponentClassAddDenied(Kwf_Component_Data $data, $componentClass);
}
