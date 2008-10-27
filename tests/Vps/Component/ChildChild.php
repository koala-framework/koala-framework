<?php
class Vps_Component_Output_ChildChild extends Vpc_Abstract
{
    public function hasContent()
    {
        return false;
    }

    public function getTemplateFile()
    {
        return dirname(__FILE__) . '/ChildChild.tpl';
    }
}
?>