<?php
class Vpc_Newsletter_Detail_MailingController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('delete', 'deleteAll');
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_modelName = 'Vpc_Newsletter_QueueModel';
    protected $_queryFields = array('searchtext');
    protected $_sortable = false;

    public function preDispatch()
    {
        $this->_editDialog = array(
            'type' => 'Vpc.Mail.PreviewWindow',
            'controllerUrl' => Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Preview'),
            'baseParams' => array(
                'componentId' => $this->_getParam('componentId')
            ),
            'width' => 700,
            'height' => 400
        );
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width' => 85
        );

        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('Firstname'), 140))
            ->setData(new Vpc_Newsletter_Detail_UserData('firstname'));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Lastname'), 140))
            ->setData(new Vpc_Newsletter_Detail_UserData('lastname'));
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('E-Mail'), 200))
            ->setData(new Vpc_Newsletter_Detail_UserData('email'));
        $this->_columns->add(new Vps_Grid_Column('format', trlVps('Format'), 60))
            ->setData(new Vpc_Newsletter_Detail_UserData('format'));
        $this->_columns->add(new Vps_Grid_Column('status', trlVps('Status'), 60));
        $this->_columns->add(new Vps_Grid_Column('sent_date', trlVps('Date Sent'), 120));
        $this->_columns->add(new Vps_Grid_Column_Button('show'))
            ->setButtonIcon(new Vps_Asset('email_open.png'));
    }

    protected function _getSelect()
    {
        $select = parent::_getSelect();
        $select->whereEquals('newsletter_id', $this->_getNewsletterRow()->id);
        return $select;
    }

    public function jsonDeleteAllAction()
    {
        $select = $this->_model->select()
            ->whereEquals('newsletter_id', $this->_getNewsletterRow()->id);
        $count = $this->_model->countRows($select);

        $select->where(new Vps_Model_Select_Expr_Or(array(
            new Vps_Model_Select_Expr_Equals('status', 'queued'),
            new Vps_Model_Select_Expr_Equals('status', 'userNotFound')
        )));
        $count2 = $this->_model->countRows($select);
        $this->_model->deleteRows($select);
        $this->view->message = trlVps(
            '{0} of {1} queued users deleted.',
            array($count2, $count)
        );
    }

    protected function _hasPermissions($row, $action)
    {
        if ($action == 'delete' && $row->status != 'queued' && $row->status != 'userNotFound') {
            throw new Vps_Exception_Client(trlVps('Can only delete queued recipients'));
        }
        return parent::_hasPermissions($row, $action);
    }

    private function _getNewsletterRow()
    {
        $newsletterId = (int)substr(strrchr($this->_getParam('componentId'), '_'), 1);
        $model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_Model');
        return $model->getRow($newsletterId);
    }

    public function jsonChangeStatusAction()
    {
        $row = $this->_getNewsletterRow();
        $status = $this->_getParam('status');
        if ($row->status != $status) {
            if ($row->status == 'stop') {
                $this->view->error = trlVps('Newsletter stopped, cannot change status.');
            } else if (in_array($status, array('start', 'pause', 'stop'))) {
                $row->status = $status;
                $row->save();
            } else {
                $this->view->error = trlVps('Unknown status.');
            }
        }
        $this->jsonStatusAction();
    }

    public function jsonDataAction()
    {
        parent::jsonDataAction();
        $this->jsonStatusAction();
    }

    public function jsonStatusAction()
    {
        $this->view->info = $this->_getNewsletterRow()->getInfo();
    }
}