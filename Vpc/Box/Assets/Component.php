<?php
class Vpc_Box_Assets_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['section'] = $this->_getSection();
        return $ret;
    }

    protected function _getSection()
    {
        return 'web';
    }
}
