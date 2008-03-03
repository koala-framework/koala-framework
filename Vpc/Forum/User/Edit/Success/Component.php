<?php
class Vpc_Forum_User_Edit_Success_Component extends Vpc_Formular_Success_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['requestUri'] = $_SERVER['REQUEST_URI'];
        return $ret;
    }
}
