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
    }

    public function preDispatch()
    {
        parent::preDispatch();
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
}