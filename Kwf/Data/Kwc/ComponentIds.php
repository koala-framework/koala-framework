<?php
class Kwf_Data_Kwc_ComponentIds extends Kwf_Data_Table
{
    public function load($row)
    {
        $ret = array('componentId' => $row->component_id,
                     'content'       => parent::load($row));
        return $ret;
    }
}
