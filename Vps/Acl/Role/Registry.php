<?p
class Vps_Acl_Role_Registry extends Zend_Acl_Role_Regist

    public function getRoles
   
        $ret = array(
        foreach ($this->_roles as $role)
            $ret[] = $role['instance'
       
        return $re
   

