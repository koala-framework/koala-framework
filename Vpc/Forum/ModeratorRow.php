<?php
class Vpc_Forum_ModeratorRow extends Vps_Db_Table_Row
{
    public function __get($columnName)
    {
        if ($columnName == 'user_out') {

            $userTable = Zend_Registry::get('userModel');
            $row = $userTable->find($this->user_id)->current();

            if ($row) {
                return $row->__toString();
            }

            return '<i>'.trlVps('Empty').'</i>';
        } else {
            return parent::__get($columnName);
        }
    }

    public function __isset($columnName)
    {
        if ($columnName == 'user_out') {
            return true;
        } else {
            return parent::__isset($columnName);
        }
    }

}
