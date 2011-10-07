<?php
class Kwc_Newsletter_Subscribe_RecipientsController extends Kwc_Newsletter_Subscribe_AbstractRecipientsController
{
    protected $_buttons = array('add', 'delete');
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_queryFields = array('id', 'email', 'firstname', 'lastname');
    protected $_modelName = 'Kwc_Newsletter_Subscribe_Model';

    public function indexAction()
    {
        parent::indexAction();
        $formControllerUrl = Kwc_Admin::getInstance($this->_getParam('class'))
            ->getControllerUrl('Recipient');

        $this->view->formControllerUrl = $formControllerUrl;
        $this->view->xtype = 'kwc.newsletter.recipients';
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

        $this->_columns->add(new Kwf_Grid_Column_Button('edit', trlKwf('Edit')));
        $this->_columns->add(new Kwf_Grid_Column('email', trlKwf('Email'), 200));
        $this->_columns->add(new Kwf_Grid_Column('gender', trlKwf('Gender'), 70))
            ->setRenderer('genderIcon');

        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 80));
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('First name'), 110));
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Last name'), 110));

        $this->_columns->add(new Kwf_Grid_Column('subscribe_date', trlKwf('Subscribe date'), 110));

        $this->_columns->add(new Kwf_Grid_Column('is_active', trlKwf('Active?'), 80))
            ->setData(new Kwc_Newsletter_Detail_IsActiveData());
    }
}