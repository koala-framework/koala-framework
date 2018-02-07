<?php
class Kwc_Newsletter_Subscribe_RecipientsController extends Kwc_Newsletter_Subscribe_AbstractRecipientsController
{
    protected $_buttons = array('add', 'unsubscribe', 'delete', 'xls');
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_queryFields = array('id', 'email', 'firstname', 'lastname');
    protected $_model = 'Kwc_Newsletter_Subscribe_Model';

    public function indexAction()
    {
        parent::indexAction();
        $admin = Kwc_Admin::getInstance($this->_getParam('class'));
        $formControllerUrl = $admin->getControllerUrl('Recipient');

        $this->view->formControllerUrl = $formControllerUrl;
        $this->view->logsControllerUrl = $admin->getControllerUrl('Logs');
        $this->view->xtype = 'kwc.newsletter.subscribe.recipients';
        $this->view->model = get_class($this->_model);
        $this->view->baseParams = array(
            'newsletterComponentId' => $this->_getParam('newsletterComponentId')
        );
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

    protected function _initColumns()
    {
        if ($formControllerUrl = $this->_getParam('formControllerUrl')) {
            $this->_editDialog = array(
                'controllerUrl' => $formControllerUrl,
                'width' => 500,
                'height' => 250
            );
        }
        parent::_initColumns();
        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width' => 85
        );

        $this->_columns->add(new Kwf_Grid_Column_Button('edit', trlKwf('Edit')));
        $this->_columns->add(new Kwf_Grid_Column('email', trlKwf('Email'), 200));
        if ($this->_model->hasColumn('gender')) {
            $this->_columns->add(new Kwf_Grid_Column('gender', trlKwf('Gender'), 70))
                ->setRenderer('genderIcon');
        }

        if ($this->_model->hasColumn('title')) {
            $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 80));
        }
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('First name'), 110));
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Last name'), 110));

        $this->_columns->add(new Kwf_Grid_Column('activated', trlKwf('Active?'), 80))
            ->setData(new Kwc_Newsletter_Detail_IsActiveData())
            ->setRenderer('newsletterState')
            ->setType('string');

        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwc_Newsletter_PluginInterface') as $plugin) {
            $plugin->modifyRecipientsGridColumns($this->_columns, Kwc_Newsletter_PluginInterface::RECIPIENTS_GRID_TYPE_EDIT_SUBSCRIBERS);
        }
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_model->hasColumn('newsletter_component_id')) {
            if ($this->_getParam('newsletterComponentId')) {
                $acl = Kwf_Registry::get('acl')->getComponentAcl();
                $c = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('newsletterComponentId'), array('ignoreVisible'=>true, 'limit'=>1));
                if (!$acl->isAllowed(Kwf_Registry::get('userModel')->getAuthedUser(), $c)) throw new Kwf_Exception_AccessDenied();
                $ret->whereEquals('newsletter_component_id', $this->_getParam('newsletterComponentId'));
            } else {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
                $ret->whereEquals('newsletter_component_id', $c->parent->dbId);
            }
        }
        return $ret;
    }

    public function jsonUnsubscribeAction()
    {
        $row = $this->_getModel()->getRow($this->_getParam('id'));
        if (!$row->unsubscribed) {
            $row->unsubscribed = true;

            $c = Kwf_Component_Data_Root::getInstance()->getComponentById($row->newsletter_component_id, array('ignoreVisible' => true));
            $user = Kwf_Registry::get('userModel')->getAuthedUser();

            $row->setLogSource($c->trlKwf('Backend'));
            $row->writeLog($c->trlKwf('Unsubscribed from {0}', array($user->name)), 'unsubscribed');

            $row->save();
        }
    }
}
