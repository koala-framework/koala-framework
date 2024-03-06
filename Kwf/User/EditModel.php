<?php
class Kwf_User_EditModel extends Kwf_Model_Proxy
{
    protected $_rowClass = 'Kwf_User_EditRow';

    private $_lock = null;
    protected $_maxLockTime = 10; // in seconds

    protected $_noLogColumns = array();

    protected $_dependentModels = array(
        'Messages' => 'Kwf_User_MessagesModel'
    );

    protected $_columnMappings = array(
        'Kwf_User_UserMapping' => array(
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'email',
            'format' => 'format',
            'gender' => 'gender',
            'title' => 'title',
            'role' => 'role',
        )
    );

    public function __construct($config = array())
    {
        if (!isset($config['proxyModel'])) {
            $config['proxyModel'] = new Kwf_Model_Db(array(
                'table' => 'kwf_users',
                'hasDeletedFlag' => true,
            ));
        }
        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['format'] = new Kwf_Model_Select_Expr_String('html');
        $this->_exprs['activated'] = new Kwf_Model_Select_Expr_And(array(
            new Kwf_Model_Select_Expr_NotEquals('password', ''),
            new Kwf_Model_Select_Expr_Not(new Kwf_Model_Select_Expr_IsNull('password'))
        ));
    }

    // wenn createRow benötigt wird weil man ein anderes userModel (db?) hat,
    // dann kann man diese hier überschreiben und return Kwf_Model_Proxy::createRow($data);
    // zurückgeben
    public function createRow(array $data=array())
    {
        $row = parent::createRow($data);
        $this->_resetPermissions($row);
        return $row;
    }

    public static function isLockedCreateUser()
    {
        $lock = fopen("temp/create-user.lock", "w");
        $ret = !flock($lock, LOCK_EX | LOCK_NB);
        fclose($lock);
        return $ret;
    }

    public function unlockCreateUser()
    {
        fclose($this->_lock);
        $this->_lock = null;
    }
    public function lockCreateUser()
    {
        if ($this->_lock) {
            throw new Kwf_Exception('Already locked');
        }
        $this->_lock = fopen("temp/create-user.lock", "w");

        $startTime = microtime(true);
        while(true) {
            if (flock($this->_lock, LOCK_EX | LOCK_NB)) {
                break;
            }
            if (microtime(true)-$startTime > $this->_maxLockTime) {
                throw new Kwf_Exception("Lock Failed, locked by");
            }
            usleep(rand(0, 100)*100);
        }
        fwrite($this->_lock, getmypid());
    }

    /**
     * @param string E-Mail address of user
     * @deprecated
     */
    public final function createUserRow($email)
    {
        return $this->createRow(array('email' => $email));
    }

    public function getKwfUserRowById($id)
    {
        $userModel = Kwf_Registry::get('userModel');
        $pm = $userModel->getProxyModel();
        if ($pm == $this) {
            return $userModel->getRow($id);
        } else if ($pm instanceof Kwf_Model_Union) {
            foreach ($pm->getUnionModels() as $k=>$m) {
                if ($m == $this) {
                    return $userModel->getRow($k.$id);
                }
            }
        }
        throw new Kwf_Exception("Can't get kwf user");
    }

    public function getRowByKwfUser($kwfUserRow)
    {
        $userModel = Kwf_Registry::get('userModel');
        if (!is_object($kwfUserRow)) $kwfUserRow = $userModel->getRow($kwfUserRow);
        $pr = $kwfUserRow->getProxiedRow();
        if ($pr->getModel() == $this) {
            return $pr;
        } else if ($pr instanceof Kwf_Model_Union_Row) {
            $ret = $pr->getSourceRow();
            if ($ret->getModel() == $this) {
                return $ret;
            }
            return null;
        }
        throw new Kwf_Exception("Can't get kwf user");
    }

    /**
     * Setzt die rechte eines neuen users zurück. Meistens wird dies beim Anlegen
     * aus einer Form sowieso überschrieben, aber sicher ist sicher. Hier könnte
     * man zB auch additionalRoles löschen.
     */
    protected function _resetPermissions($row)
    {
        $row->role = 'guest';
    }

    public function getNoLogColumns()
    {
        return $this->_noLogColumns;
    }

    public function synchronize()
    {
        //can be overridden if proxy is a mirror model
    }

    public function getAuthMethods()
    {
        return array(
            'password' => new Kwf_User_Auth_PasswordFields(
                $this
            ),
            'autoLogin' => new Kwf_User_Auth_AutoLoginFields(
                $this
            ),
            'activation' => new Kwf_User_Auth_ActivationFields(
                $this
            )
        );
    }

    public function logLogin(Kwf_Model_Row_Interface $row)
    {
        if (!$row->logins) $row->logins = 0;
        $row->logins = $row->logins + 1;
        $row->last_login = date('Y-m-d H:i:s');
        $row->writeLog("login");
        $row->save();
    }
}
