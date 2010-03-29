<?php
class Vpc_Basic_Download_Admin extends Vpc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = parent::gridColumns();
        unset($ret['string']);
        return $ret;
    }
}
