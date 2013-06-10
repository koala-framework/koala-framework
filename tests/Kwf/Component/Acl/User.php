<?php
class Kwf_Component_Acl_User
{
    public $role = 'guest';
    private $_additionalRoles = array();

    public function __construct($role, $additionalRoles)
    {
        $this->role = $role;
        $this->_additionalRoles = $additionalRoles;
    }

    public function getAdditionalRoles()
    {
        return $this->_additionalRoles;
    }
}
