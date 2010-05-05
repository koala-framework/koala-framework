<?php
class Vpc_Posts_Directory_Row extends Vps_Model_Proxy_Row
{
    protected function _beforeInsert()
    {
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        if (!$this->user_id && $user) {
            $this->user_id = $user->id;
        }
    }
}
