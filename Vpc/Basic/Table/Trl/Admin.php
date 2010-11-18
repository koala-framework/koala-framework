<?php
class Vpc_Basic_Table_Trl_Admin extends Vpc_Basic_Table_Admin
{
    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        unset($ret['settings']);
        return $ret;
    }
}
