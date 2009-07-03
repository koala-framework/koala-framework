<?php
class Vpc_Newsletter_Detail_RecipientsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('saveRecipients');
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_queryFields = array('id', 'email', 'firstname', 'lastname');

    public function preDispatch()
    {
        $this->_model = Zend_Registry::get('userModel');
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width' => 85
        );

        $this->_columns->add(new Vps_Grid_Column('email', trlVps('Email'), 200));
        $this->_columns->add(new Vps_Grid_Column('gender', trlVps('Gender'), 70))
            ->setRenderer('genderIcon');
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 80));

        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('First name'), 110));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Last name'), 110));
    }

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'vpc.newsletter.recipientsPanel';
        $this->view->model = get_class($this->_model);
    }

    public function jsonSaveRecipientsAction()
    {
        $component = Vps_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId'), array('ignoreVisible'=>true)
        );

        $order = $this->_defaultOrder;
        if ($this->getRequest()->getParam('sort')) {
            $order['field'] = $this->getRequest()->getParam('sort');
        }
        if ($this->_getParam("direction") && $this->_getParam('direction') != 'undefined') {
            $order['direction'] = $this->_getParam('direction');
        }
        $select = $this->_getSelect();
        if (is_null($select)) return null;
        $select->order($order);
        foreach ($this->_model->getRows($select) as $row) {
            $component->getComponent()->addToQueue($row);
        }
        $this->view->assign($component->getComponent()->saveQueue());
    }
}