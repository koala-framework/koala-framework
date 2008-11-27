<?php
class Vps_Data_Vpc_ComponentIds extends Vps_Data_Table
{
    public function load($row)
    {
        $ret = array('componentId' => $row->component_id,
                     'content'       => parent::load($row));
        return $ret;
    }
}
