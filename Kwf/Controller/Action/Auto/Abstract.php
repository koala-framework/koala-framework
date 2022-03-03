<?php
abstract class Kwf_Controller_Action_Auto_Abstract extends Kwf_Controller_Action
{
    protected $_buttons = array();
    protected $_permissions;
    private $_helpText;

    public function init()
    {
        parent::init();


        if (!isset($this->_permissions)) {
            $this->_permissions = $this->_buttons;
        }

        $btns = array();
        foreach ($this->_buttons as $k=>$i) {
            if (is_int($k)) {
                $btns[$i] = true;
            } else {
                $btns[$k] = $i;
            }
        }
        $this->_buttons = $btns;

        $perms = array();
        foreach ($this->_permissions as $k=>$i) {
            if (is_int($k)) {
                $perms[$i] = true;
            } else {
                $perms[$k] = $i;
            }
        }
        $this->_permissions = $perms;
    }

    public final function setHelpText($helpText)
    {
        $this->_helpText = $helpText;
    }

    public final function getHelpText()
    {
        return $this->_helpText;
    }

    public function postDispatch()
    {
        $userActionsLogConfig = $this->_getUserActionsLogConfig();
        $action = $this->getRequest()->getActionName();
        if (substr($action, 0, 5) == 'json-' && !in_array($action, $userActionsLogConfig['ignoreActions'])) {
            $this->_logUserAction($userActionsLogConfig['componentId'], $userActionsLogConfig['details']);
        }
        parent::postDispatch();
    }

    protected function _getUserActionsLogConfig()
    {
        return array(
            'ignoreActions' => array(
                'json-index', 'json-meta',
                'json-load', 'json-data',
                'json-copy', 'json-paste',
                'json-expand', 'json-collapse',
                'json-csv', 'json-xls',
                'json-progress-status', 'json-status'
            ),
            'componentId' => $this->_getParam('componentId'),
            'details' => null,
        );
    }

    private function _logUserAction($componentId, $details)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($componentId, array('ignoreVisible' => true));
        if (!$component) return;

        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        if (!$user) return;

        $data = array(
            'user_name' => $user->name,
            'user_email' => $user->email,
            'url' => '',
            'domain' => $component->getDomainComponent()->name,
            'details' => $details,
        );
        $page = $component->getPage();
        if ($page) {
            $data['url'] = $page->ownUrl;
        }
        if (!$data['details']) {
            $componentForDetails = $component;
            while ($componentForDetails && (!isset($componentForDetails->generator) || !$componentForDetails->generator->getGeneratorFlag('box'))) {
                $componentForDetails = $componentForDetails->parent;
            }
            if (!$componentForDetails) {
                $componentForDetails = $page;
            }
            if (!$componentForDetails) {
                $componentForDetails = $component;
            }
            if (Kwc_Abstract::hasSetting($componentForDetails->componentClass, 'componentName')) {
                $data['details'] = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($componentForDetails->componentClass, 'componentName'));
            }
        }

        $model = Kwf_Model_Abstract::getInstance('Kwf_User_ActionsLogModel');
        $select = $model->select()
            ->whereEquals('user_email', $data['user_email'])
            ->order('date', 'DESC');
        $lastRow = $model->getRow($select);
        if ($lastRow &&
            $lastRow->domain == $data['domain'] &&
            $lastRow->url == $data['url'] &&
            $lastRow->details == $data['details']
        ) {
            $lastRow->changes++;
            $lastRow->date = date('Y-m-d H:i:s');
            $lastRow->save();
        } else {
            $data['changes'] = 1;
            $data['date'] = date('Y-m-d H:i:s');
            $model->createRow($data)->save();
        }
    }
}
