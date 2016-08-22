<?php
class Kwf_Component_Cache_Mysql_Root_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_Cache_Mysql_Root_Model';
        return $ret;
    }
}
