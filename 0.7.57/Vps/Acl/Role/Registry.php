<?php
class Vps_Acl_Role_Registry extends Zend_Acl_Role_Registry
{
    public function getRoles()
    {
        $ret = array();
        foreach ($this->_roles as $role) {
            $ret[] = $role['instance'];
        }
        return $ret;
    }
}
