<?p
class Vps_Controller_Action_Auto_Abstract extends Vps_Controller_Acti

    protected $_buttons = array(
    protected $_permission

    public function init
   
        parent::init(

        if (!isset($this->_permissions))
            $this->_permissions = $this->_button
       

        $btns = array(
        foreach ($this->_buttons as $k=>$i)
            if (is_int($k))
                $btns[$i] = tru
            } else
                $btns[$k] = $
           
       
        $this->_buttons = $btn

        $perms = array(
        foreach ($this->_permissions as $k=>$i)
            if (is_int($k))
                $perms[$i] = tru
            } else
                $perms[$k] = $
           
       
        $this->_permissions = $perm

        //buttons/permissions abhängig von privileges in acl ausblenden/lösch
        $acl = $this->_getAcl(
        $role = $this->_getUserRole(
        $resource = $this->_getResourceName(

        foreach ($this->_buttons as $k=>$i)
            if (!$acl->isAllowed($role, $resource, $k))
                unset($this->_buttons[$k]
           
       
        foreach ($this->_permissions as $k=>$i)
            if (!$acl->isAllowed($role, $resource, $k))
                unset($this->_permissions[$k]
           
       
   

