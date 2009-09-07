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
        $this->_model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_Subscribe_Model');
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

        $this->_columns->add(new Vps_Grid_Column('subscribe_date', trlVps('Subscribe date'), 110));

        $this->_columns->add(new Vps_Grid_Column_Checkbox('unsubscribed', trlVps('Unsubscribed')));
    }

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'vpc.newsletter.recipientsPanel';
        $this->view->model = get_class($this->_model);
    }

    public function jsonSaveRecipientsAction()
    {
        set_time_limit(60*10);

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
        $rowset = $this->_model->getRows($select);
        $count = count($rowset);

        $progressBar = new Zend_ProgressBar(
            new Vps_Util_ProgressBar_Adapter_Cache(
                $this->_getParam('progressNum')
            ), 0, $count
        );
        $x = 0;
        foreach ($rowset as $row) {
            $x++;
            $component->getComponent()->addToQueue($row);
            $progressBar->next(1, "$x / $count");
        }
        $this->view->assign($component->getComponent()->saveQueue());
    }
}