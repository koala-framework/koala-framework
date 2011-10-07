<?php
class Kwc_Composite_LinkImage_Admin extends Kwc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = parent::gridColumns();
        unset($ret['string']);
        return $ret;
    }
}
