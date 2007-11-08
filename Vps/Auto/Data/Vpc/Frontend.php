<?php
class Vps_Auto_Data_Vpc_Frontend extends Vps_Auto_Data_Abstract
{
    public function load($row)
    {
        $class = $row->component_class;
        $id = $row->page_id . $row->component_key . '-' . $row->id;
        return "/admin/component/show/$class/$id";
    }
}