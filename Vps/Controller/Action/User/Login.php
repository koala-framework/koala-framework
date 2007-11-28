<?p
class Vps_Controller_Action_User_Login extends Vps_Controller_Acti

    public function indexAction
   
        $location = $this->_getParam('location'
        if ($location == '') { $location = '/';
        $config = array('location' => $location)
        $this->view->ext('Vps.User.Login.Index', $config
   

    public function showFormAction
   
        $this->view->ext(''
        $this->view->setRenderFile(VPS_PATH . '/views/Login.html'
        $this->view->username = '
        if ($this->_getParam('username'))
            $result = $this->_login(
            $this->view->username = $this->_getParam('username'
            if ($result->isValid())
                $this->view->text = 'Login successful.<!--successful->
            } else
                $this->view->text = 'Login failed
           
        } else
            $this->view->text = '
       
   

    public function logoutAction
   
        Zend_Auth::getInstance()->clearIdentity(
        $this->_onLogout(
   

    public function jsonLoginAction
   
        $this->view->login = tru
        $this->view->success = fals
   

    public function jsonLoginUserAction
   
        $result = $this->_login(
        if (!$result->isValid())
            $this->view->error = implode("<br />", $result->getMessages()
       
   

    public function jsonLogoutUserAction
   
        $this->logoutAction(
   

    protected function _createAuthAdapter
   
        $dao = Zend_Registry::get('dao'
        $adapter = new Zend_Auth_Adapter_DbTable($dao->getDb(), 'vps_users', 'username', 'password', 'MD5(CONCAT(?, password_salt))'
        return $adapte
   

    private function _login
   
        $username = $this->getRequest()->getParam('username'
        $password = $this->getRequest()->getParam('password'
        $adapter = $this->_createAuthAdapter(

        if (!$adapter instanceof Zend_Auth_Adapter_DbTable)
            throw new Vps_Controller_Exception('_createAuthAdapter didn\'t return instance of Zend_Auth_Adapter_DbTable'
       

        $auth = Zend_Auth::getInstance(
        $adapter->setIdentity($username
        $adapter->setCredential($password
        $result = $auth->authenticate($adapter
        if ($result->isValid())
            $resultRow = $adapter->getResultRowObject(null, array('password', 'password_salt')
            $auth->getStorage()->write($resultRow
            $this->_onLogin(
       
        return $resul
   

    protected function _onLogin
   
   

    protected function _onLogout
   
   

