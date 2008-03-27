<?php
class Vpc_Forum_Column_Moderator extends Vps_Auto_Grid_Column
{
    public function load($row, $role)
    {
        $v = parent::load($row, $role);
        d($v);

        $userTable = Zend_Registry::get('userModel');
        $row = $userTable->find($row->user_id)->current();

        if ($row) {
            return $row->__toString();
        }

        return $v;
    }
}
