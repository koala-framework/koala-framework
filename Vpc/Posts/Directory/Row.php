<?php
class Vpc_Posts_Directory_Row extends Vpc_Abstract_Composite_Row
{
//     public function __toString()
//     {
//         return $this->id;
//     }

    protected function _insert()
    {
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        if (!$this->user_id && $user) {
            $this->user_id = $user->id;
        }
    }
}
