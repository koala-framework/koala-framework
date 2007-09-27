<?php
class Vps_Auto_Grid_Column_Empty extends Vps_Auto_Grid_Column
{
    public function getData($row, $role)
    {
        return null;
    }
    public function getMetaData($tableInfo = null)
    {
        $ret = parent::getMetaData($tableInfo);
        $ret['sortable'] = false;
        $ret['groupable'] = false;
        return $ret;
    }
}
