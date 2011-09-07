<?php
class Vpc_Box_TitleEditable_Component extends Vpc_Box_Title_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Title');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    protected function _getTitle()
    {
        if (trim($this->_getRow()->title)) return $this->_getRow()->title;
        return parent::_getTitle();
    }
}
