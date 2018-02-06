<?php
class Kwc_Newsletter_Subscribe_RecipientController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_formName = 'Kwc_Newsletter_EditSubscriber_Form';

    public function preDispatch()
    {
        if (!isset($this->_form)) {
            if (isset($this->_formName)) {
                $this->_form = new $this->_formName('form', $this->_getParam('class'), $this->_getParam('newsletterComponentId'));
            }
        }
        parent::preDispatch();

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('newsletterComponentId'), array('ignoreVisible' => true));
        $this->_form->getRow()->setLogSource($c->trlKwf('Backend'));
    }

    protected function _isAllowedComponent()
    {
        $authData = $this->_getAuthData();
        $class = $this->_getParam('class');
        if (!Kwf_Registry::get('acl')->isAllowedComponent($class, $authData)) return false;

        $nlComponentId = $this->_getParam('newsletterComponentId');
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($nlComponentId, array('ignoreVisible'=>true));
        return Kwf_Registry::get('acl')->isAllowedComponentById($nlComponentId, $component->componentClass, $authData);
    }

    protected function _hasPermissions($row, $action)
    {
        $ret = parent::_hasPermissions($row, $action);
        if ($ret) {
            if ($row->newsletter_component_id != $this->_getParam('newsletterComponentId')) {
                return false;
            }
        }
        return $ret;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->newsletter_component_id = $this->_getParam('newsletterComponentId');

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('newsletterComponentId'), array('ignoreVisible' => true));
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        $row->setLogSource($c->trlKwf('Backend'));
        $row->writeLog($c->trlKwf('Subscribed by {0}', array($user->name)));
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);

        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Kwf_Registry::get('config')->server->domain;
        }

        $root = Kwf_Component_Data_Root::getInstance();
        $nlData = $root->getComponentById($this->_getParam('newsletterComponentId'), array('ignoreVisible' => true));
        $subscribeData = $root->getComponentByClass('Kwc_Newsletter_Subscribe_Component', array('ignoreVisible' => true, 'subroot' => $nlData->getSubroot()));

        $editComponent = $nlData->getChildComponent('_editSubscriber');
        $doubleOptInComponent = $subscribeData->getChildComponent('_doubleOptIn');

        $mail = $subscribeData->getChildComponent('_mail')->getComponent();
        $mail->send($row, array(
            'formRow' => $row,
            'host' => $host,
            'editComponent' => $editComponent,
            'doubleOptInComponent' => $doubleOptInComponent
        ));
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);

        if ($row->id) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('newsletterComponentId'), array('ignoreVisible' => true));

            $row->setLogSource($c->trlKwf('Backend'));
            $user = Kwf_Registry::get('userModel')->getAuthedUser();
            $logMessages = array(
                $c->trlKwf('Changed data by {0}', array($user->name))
            );
            foreach ($row->getDirtyColumns() as $column) {
                $columnName = $column;

                switch ($column) {
                    case 'gender':
                        $columnName = $c->trlKwf('Gender');
                        break;
                    case 'title':
                        $columnName = $c->trlKwf('Title');
                        break;
                    case 'firstname':
                        $columnName = $c->trlKwf('Firstname');
                        break;
                    case 'lastname':
                        $columnName = $c->trlKwf('Lastname');
                        break;
                    case 'email':
                        $columnName = $c->trlKwf('Email');
                        break;
                }

                $logMessages[] = $c->trlKwf('{0}: "{1}" to "{2}"', array($columnName, $row->getCleanValue($column), $row->{$column}));
            }

            if (count($logMessages) > 1) $row->writeLog(implode("\n", $logMessages));
        }
    }
}
