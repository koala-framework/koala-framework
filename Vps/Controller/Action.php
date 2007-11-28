<?p
class Vps_Controller_Action extends Zend_Controller_Acti

    protected $_auth = fals

    public function jsonIndexAction
   
        $this->indexAction(
   

    public function preDispatch
   
        if (!$this instanceof Vps_Controller_Action_Err
                && $this->_getParam('application_version
                && $this->getHelper('ViewRenderer')->isJson())
            $version = Zend_Registry::get('config')->application->versio
            if ($version != $this->_getParam('application_version'))
                $this->_forward('jsonWrongVersion', 'error', 'vps'
           
       

        $acl = $this->_getAcl(
        $role = $this->_getUserRole(
        $resource = $this->_getResourceName(

        if (!$acl->isAllowed($role, $resource, 'view'))
            if ($this->getHelper('ViewRenderer')->isJson())
                $this->_forward('jsonLogin', 'login', 'vps'
            } else
                $params = array('location' => $this->getRequest()->getPathInfo()
                $this->_forward('index', 'login', 'vps', $params
           
       
   

    protected function _getResourceName
   
        $resource = strtolower(str_replace('Controller', '', str_replace('Vps_Controller_Action_Component_', '', get_class($this)))
        if (substr($resource, 0, 4) == 'vpc_')
            $resource = 'component
       
        return $resourc
   

    protected function _getUserRole
   
        return $this->_getAuthData() ? $this->_getAuthData()->role : 'guest
   

    protected function _getAuthData
   
        return Zend_Auth::getInstance()->getStorage()->read(
   

    protected function _getAcl
   
        if (!Zend_Registry::isRegistered('acl'))
            $acl = new Vps_Acl(
            Zend_Registry::set('acl', $acl
       
        return Zend_Registry::get('acl'
   


