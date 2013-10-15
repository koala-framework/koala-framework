<?php
class Kwf_Component_Cache_Mysql_Root_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_Cache_Mysql_Root_Model';
        return $ret;
    }
}
