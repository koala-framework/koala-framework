<?php
class Kwf_Component_RenderTwig_TestCmp_Child_Component extends Kwf_Component_RenderTwig_Parent_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        return $ret;
    }
}
