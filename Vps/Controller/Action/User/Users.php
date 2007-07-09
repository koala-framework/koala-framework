<?php

class Vps_Controller_Action_User_Users extends Vps_Controller_Action_Auto_Grid
{
    protected $_gridColumns = array(
            array('dataIndex' => 'active',
                  'header'    => 'Aktiv',
                  'width'     => 40,
                  'renderer'  => 'Boolean',
                  'editor'    => 'Checkbox'),
            array('dataIndex' => 'username',
                  'header'    => 'Benutzer',
                  'width'     => 140,
                  'editor'    => 'TextField'),
            array('dataIndex' => 'role',
                  'header'    => 'Berechtigung',
                  'editor'    => array('type'=>'ComboBox',
                                       'mode'      => 'local',
                                       'store'     => array('data' => array()),
                                       'editable'  => false,
                                       'triggerAction'=>'all')),
            array('dataIndex' => 'realname',
                  'header'    => 'Name',
                  'width'     => 200,
                  'editor'    => 'TextField'),
            array('dataIndex' => 'email',
                  'header'    => 'E-Mail',
                  'width'     => 250,
                  'editor'    => 'TextField'),
            array('dataIndex' => 'password_mailed',
                  'header'    => 'Passwort gemailt',
                  'width'     => 100,
                  'renderer'  => 'Boolean',
                  'editor'    => 'TextField')
            );
    protected $_gridButtons = array('save'=>true,
                                    'add'=>true,
                                    'delete'=>true);
    protected $_gridPaging = 0;
    protected $_gridDefaultOrder = 'username';
    protected $_gridTableName = 'Vps_Model_User_Users';
    
    public function init()
    {
        foreach ($this->_gridColumns as $k=>$c) {
            if (isset($c['dataIndex']) && $c['dataIndex'] == 'role') {
                $acl = Zend_Registry::get('acl');
                $roles = $acl->getRoles();
                foreach($roles as $role) {
                    if($role instanceof Vps_Acl_Role) {
                        $this->_gridColumns[$k]['editor']['store']['data'][] = array($role->getRoleId(), $role->getRoleName());
                    }
                }
            }
        }
        parent::init();
    }

    public function indexAction()
    {
        $this->view->ext('Vps.User.Users');
    }

    public function jsonMailsendAction()
    {
        $success = false;
        $request = $this->getRequest();
        $id = $request->getParam('id');

        if ($user = $this->_gridTable->find($id)->current()) {
            if ($user->email) {
                $user->sendPasswordMail();
                $user->save();
                $success = true;
            } else {
                $error = 'E-Mail wurde nicht gesendet, da keine E-Mail-Adresse für diesen Benutzer gefunden wurde.<br><br>'
                        .'Das alte Passwort bleibt erhalten.';
                $this->view->error = $error;
            }
        }
        $this->view->success = $success;
    }
}
