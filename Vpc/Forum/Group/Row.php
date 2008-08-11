<?php
class Vpc_Forum_Group_Row extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->subject;
    }

    protected function _insert()
    {
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        if (!$this->user_id && $user) {
            $this->user_id = $user->id;
        }
    }
}