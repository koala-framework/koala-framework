<?php
class Vpc_Abstract_ListRandom_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($ret['children']) {
            $randKey = array_rand($ret['children']);
            $ret['child'] = $ret['children'][$randKey];
            $ret['children'] = array($ret['child']);
        }
        return $ret;
    }

}
