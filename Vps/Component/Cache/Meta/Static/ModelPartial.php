<?php
class Vps_Component_Cache_Meta_Static_ModelPartial extends Vps_Component_Cache_Meta_Static_Model
{
    public static function getDeleteWhere($pattern, $row)
    {
        $ret = parent::getDeleteWhere($pattern, $row);
        $ret['type'] = array('partial');
        return $ret;
    }
}