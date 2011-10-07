<?php
class Kwc_Posts_Directory_Row extends Kwf_Model_Proxy_Row
{
    protected function _beforeInsert()
    {
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        if (!$this->user_id && $user) {
            $this->user_id = $user->id;
        }
    }
}
