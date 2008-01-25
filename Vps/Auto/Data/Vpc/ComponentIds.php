<?php
class Vps_Auto_Data_Vpc_ComponentIds extends Vps_Auto_Data_Table
{
    public function load($row)
    {
        $ret = array('component_id' => $row->component_id,
                     'content'       => parent::load($row));
        return $ret;
    }
}
