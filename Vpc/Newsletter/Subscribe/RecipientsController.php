<?php
class Vpc_Newsletter_Subscribe_RecipientsController extends Vpc_Newsletter_Subscribe_AbstractRecipientsController
{
    protected $_buttons = array('add', 'delete');
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_queryFields = array('id', 'email', 'firstname', 'lastname');
    protected $_modelName = 'Vpc_Newsletter_Subscribe_Model';

    public function indexAction()
    {
        parent::indexAction();
        $formControllerUrl = Vpc_Admin::getInstance($this->_getParam('class'))
            ->getControllerUrl('Recipient');

        $this->view->formControllerUrl = $formControllerUrl;
        $this->view->xtype = 'vpc.newsletter.recipients';
        $this->view->model = get_class($this->_model);
        $this->view->baseParams = array(
            'newsletterComponentId' => $this->_getParam('newsletterComponentId')
        );
    }

    protected function _isAllowedComponent()
    {
        $authData = $this->_getAuthData();
        $class = $this->_getParam('class');
        if (!Vps_Registry::get('acl')->isAllowedComponent($class, $authData)) return false;

        $nlComponentId = $this->_getParam('newsletterComponentId');
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($nlComponentId, array('ignoreVisible'=>true));
        return Vps_Registry::get('acl')->isAllowedComponentById($nlComponentId, $component->componentClass, $authData);
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width' => 85
        );

        $this->_columns->add(new Vps_Grid_Column_Button('edit', trlVps('Edit')));
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('Email'), 200));
        $this->_columns->add(new Vps_Grid_Column('gender', trlVps('Gender'), 70))
            ->setRenderer('genderIcon');

        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 80));
        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('First name'), 110));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Last name'), 110));

        $this->_columns->add(new Vps_Grid_Column('subscribe_date', trlVps('Subscribe date'), 110));

        $this->_columns->add(new Vps_Grid_Column('is_active', trlVps('Active?'), 80))
            ->setData(new Vpc_Newsletter_Detail_IsActiveData());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_getParam('newsletterComponentId')) {
            $ret->whereEquals('newsletter_component_id', $this->_getParam('newsletterComponentId'));
        } else {
            $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
            $ret->whereEquals('newsletter_component_id', $c->parent->dbId);
        }
        return $ret;
    }
}