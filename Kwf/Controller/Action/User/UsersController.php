<?php
class Kwf_Controller_Action_User_UsersController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add', 'userlock', 'userdelete', 'xls'); // original delete button entfernt
    protected $_permissions = array('userlock' => true, 'userdelete' => true, 'xls' => true);
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_queryFields = array('id', 'email', 'firstname', 'lastname');
    protected $_editDialog = array('controllerUrl'=>'/kwf/user/user',
                                   'width'=>550,
                                   'height'=>520);

    public function preDispatch()
    {
        $this->_model = Kwf_Config::getValue('user.kwfUserController.model');
        $this->_editDialog['controllerUrl'] = $this->getRequest()->getBaseUrl().$this->_editDialog['controllerUrl'];
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width' => 85
        );
        $this->_filters['lockedtoo'] = array(
            'type'      => 'Button',
            'skipWhere' => true,
            'icon'      => $this->getRequest()->getBaseUrl().'/assets/silkicons/user_red.png',
            'cls'       => 'x2-btn-text-icon',
            'text'      => trlKwf('Show locked users'),
            'tooltip'   => trlKwf('Show locked users too')
        );

        // alle erlaubten haupt-rollen in variable
        $roles = array();
        $acl = Kwf_Registry::get('acl');
        $userRole = Kwf_Registry::get('userModel')->getAuthedUserRole();
        foreach ($acl->getAllowedEditRolesByRole($userRole) as $role) {
            $roleName = Kwf_Trl::getInstance()->trlStaticExecute($role->getRoleName());
            $roles[] = array($role->getRoleId(), $roleName);
        }
        $this->_filters['role'] = array(
            'type'=>'ComboBox',
            'width'=>120,
            'label' => trlKwf('Rights').':',
            'defaultText' => trlKwf('all'),
            'skipWhere' => true,
            'data' => $roles
        );

        $this->_columns->add(new Kwf_Grid_Column_Button('edit', trlKwf('Edit')));
        $this->_columns->add(new Kwf_Grid_Column('id', 'ID', 50));
        $this->_columns->add(new Kwf_Grid_Column('email', trlKwf('Email'), 140));
        $this->_columns->add(new Kwf_Grid_Column('role', trlKwf('Rights')))
            ->setData(new Kwf_Controller_Action_User_Users_RoleData());

        $this->_columns->add(new Kwf_Grid_Column('gender', trlKwf('Gender'), 70))
            ->setRenderer('genderIcon');
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 80));

        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('First name'), 110));
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Last name'), 110));

        if (isset($this->_getAuthData()->language)) {
             $this->_columns->add(new Kwf_Grid_Column('language', trlKwf('lang'), 30));
        }

        $this->_columns->add(new Kwf_Grid_Column('password', trlKwf('Activated'), 60))
            ->setRenderer('boolean')
            ->setShowIn(Kwf_Grid_Column::SHOW_IN_ALL ^ Kwf_Grid_Column::SHOW_IN_XLS);
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('locked', trlKwf('Locked'), 60));

        $authedRole = Zend_Registry::get('userModel')->getAuthedUserRole();
        $acl = Zend_Registry::get('acl');
        if ($acl->getRole($authedRole) instanceof Kwf_Acl_Role_Admin && $this->_model instanceof Kwf_User_Service_Model) {
            $this->_columns->add(new Kwf_Grid_Column_Checkbox('webcode', trlKwf('Only for this web'), 110))
                 ->setData(new Kwf_Controller_Action_User_Users_WebcodeData())
                 ->setShowIn(Kwf_Grid_Column::SHOW_IN_ALL ^ Kwf_Grid_Column::SHOW_IN_XLS);
        }
        $this->_columns->add(new Kwf_Grid_Column_Button('resend_mails', trlKwf('E-Mails')))
            ->setTooltip(trlKwf('Sent E-Mail again'))
            ->setButtonIcon(new Kwf_Asset('email_go.png'));
    }

    public function jsonUserDeleteAction()
    {
        if (!isset($this->_permissions['userdelete']) || !$this->_permissions['userdelete']) {
            throw new Kwf_Exception("userdelete is not allowed.");
        }
        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);


        $ownUserRow = $this->_model->getRowByRowByKwfUser(Kwf_Registry::get('userModel')->getAuthedUser());
        if (in_array($ownUserRow->id, $ids)) {
            throw new Kwf_ClientException(trlKwf("You cannot delete your own account."));
        }

        foreach ($ids as $id) {
            $row = $this->_model->getRow($id);
            if (!$row) {
                throw new Kwf_ClientException("Can't find row with id '$id'.");
            }
            if (!$this->_hasPermissions($row, 'userdelete')) {
                throw new Kwf_Exception("You don't have the permissions to delete this user.");
            }
            $row->deleted = 1;
            $row->save();
        }
    }

    public function jsonUserLockAction()
    {
        if (!isset($this->_permissions['userlock']) || !$this->_permissions['userlock']) {
            throw new Kwf_Exception("userlock is not allowed.");
        }
        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);

        $ownUserRow = $this->_model->getRowByRowByKwfUser(Kwf_Registry::get('userModel')->getAuthedUser());
        if (in_array($ownUserRow->id, $ids)) {
            throw new Kwf_ClientException(trlKwf("You cannot lock your own account."));
        }

        foreach ($ids as $id) {
            $row = $this->_model->getRow($id);
            if (!$row) {
                throw new Kwf_ClientException("Can't find row with id '$id'.");
            }
            if (!$this->_hasPermissions($row, 'userlock')) {
                throw new Kwf_Exception("You don't have the permissions to lock this user.");
            }
            $row->locked = $row->locked ? 0 : 1;
            $row->save();

            $this->_model->writeLog(array(
                'user_id' => $row->id,
                'message_type' => ($row->locked ? 'user_locked' : 'user_unlocked')
            ));
        }
    }

    public function jsonResendMailAction()
    {
        $userId = $this->getRequest()->getParam('user_id');
        $type = $this->getRequest()->getParam('mailtype');

        if (!$userId || !$type) {
            throw new Kwf_Exception("Wrong parameters submitted");
        }

        $row = $this->_model->getRow($userId);
        if (!$row) {
            throw new Kwf_Exception("User row not found");
        }
        if (!$this->_hasPermissions($row, 'mail')) {
            throw new Kwf_Exception("Don't have permissions");
        }
        if ($type == 'activation') {
            $row->sendActivationMail();
        } else if ($type == 'lost_password') {
            $row->sendLostPasswordMail();
        }
    }

    protected function _getSelect()
    {
        $select = parent::_getSelect();
        $acl = Zend_Registry::get('acl');

        if (!($acl->getRole($this->_getUserRole()) instanceof Kwf_Acl_Role_Admin)) { //admin always sees all roles
            $roles = array();
            foreach ($acl->getAllResources() as $res) {
                if ($res instanceof Kwf_Acl_Resource_EditRole
                    && $acl->isAllowed($this->_getUserRole(), $res, 'view')
                ) {
                    $roles[] = $res->getRoleId();
                }
            }

            if ($roles) {
                $select->whereEquals('role', $roles);
            } else {
                $select = null;
            }
        }

        if (!$this->_getParam('query_lockedtoo')) {
            $select->whereEquals('locked', 0);
        }

        if ($this->_getParam('query_role')) {
            if (in_array($this->_getParam('query_role'), $roles)) {
                $select->whereEquals('role', $this->_getParam('query_role'));
            } else {
                return null;
            }
        }

        return $select;
    }

    public function indexAction()
    {
        $config = array(
            'controllerUrl' => $this->getRequest()->getBaseUrl().$this->getRequest()->getPathInfo()
        );
        if (Kwf_Registry::get('acl')->has('kwf_user_log')) {
            $config['logControllerUrl'] = $this->getRequest()->getBaseUrl().'/kwf/user/log';
        }
        if (Kwf_Registry::get('acl')->has('kwf_user_comments')) {
            $config['commentsControllerUrl'] = $this->getRequest()->getBaseUrl().'/kwf/user/comments';
        }
        $this->view->ext('Kwf.User.Grid.Index', $config);
    }

    protected function _hasPermissions($row, $action)
    {
        $acl = Kwf_Registry::get('acl');
        $userRole = Kwf_Registry::get('userModel')->getAuthedUserRole();

        if ($acl->getRole($userRole) instanceof Kwf_Acl_Role_Admin) { //admin always sees all roles
            return true;
        }

        $roles = array();
        foreach ($acl->getAllowedEditRolesByRole($userRole) as $role) {
            $roles[$role->getRoleId()] = $role->getRoleName();
        }
        if (!$roles) return false;

        if (!$row || !array_key_exists($row->role, $roles)) {
            return false;
        }

        return true;
    }
}
