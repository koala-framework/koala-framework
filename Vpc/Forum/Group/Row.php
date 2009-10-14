<?php
class Vpc_Forum_Group_Row extends Vps_Model_Db_Row
{
    public function __toString()
    {
        return $this->subject;
    }

    protected function _beforeInsert()
    {
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        if (!$this->user_id && $user) {
            $this->user_id = $user->id;
        }
        parent::_beforeInsert();
    }
}