<?php
class Vpc_Menu_Admin extends Vpc_Admin
{
    public function clearCache($caller)
    {
        if ($caller instanceof Vps_Dao_TreeCache) {
            Vps_Component_Cache::getInstance()->cleanByTag($this->_class);
        }
    }
}
