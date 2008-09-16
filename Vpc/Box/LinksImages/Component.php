<?php
class Vpc_Box_LinksImages_Component extends Vpc_Composite_LinksImages_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Box_LinksImages_LinkImage_Component';
        $ret['viewCache'] = false;
        $ret['random'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($this->_getSetting('random')) {
            $randKey = array_rand($ret['children']);
            $ret['children'] = array($ret['children'][$randKey]);
        }
        return $ret;
    }

}
