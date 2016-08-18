<?php
class Kwc_Basic_LinkTag_Abstract_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_Abstract_Cc_Data';
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getData()->url) {
            return true;
        } else {
            return false;
        }
    }
}
