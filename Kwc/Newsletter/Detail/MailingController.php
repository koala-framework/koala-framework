<?php
class Kwc_Newsletter_Detail_MailingController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('delete', 'deleteAll');
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_modelName = 'Kwc_Newsletter_QueueModel';
    protected $_queryFields = array('searchtext');
    protected $_sortable = false;
    private $_newsletterRow;

    public function preDispatch()
    {
        $this->_editDialog = array(
            'type' => 'Kwc.Mail.PreviewWindow',
            'controllerUrl' => Kwc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Preview'),
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

        $this->_columns->add(new Kwf_Grid_Column('email', trlKwf('E-Mail'), 200))
            ->setData(new Kwc_Newsletter_Detail_UserData('email'));
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('Firstname'), 140))
            ->setData(new Kwc_Newsletter_Detail_UserData('firstname'));
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Lastname'), 140))
            ->setData(new Kwc_Newsletter_Detail_UserData('lastname'));
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

        $count2 = $this->_model->countRows($select);
        $this->_model->deleteRows($select);
        $this->view->message = trlKwf(
            '{0} of {1} queued users deleted.',
            array($count2, $count)
        );
    }

    private function _getNewsletterRow()
    {
        if (!$this->_newsletterRow) {
            $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
                $this->_getParam('componentId')
            );
            $this->_newsletterRow = $component->row;
        }
        return $this->_newsletterRow;
    }

    public function jsonChangeStatusAction()
    {
        $row = $this->_getNewsletterRow();
        $status = $this->_getParam('status');
        if ($row->status != $status) {
            if ($row->status == 'stop') {
                $this->view->error = trlKwf('Newsletter stopped, cannot change status.');
            } else if (in_array($status, array('start', 'pause', 'stop'))) {
                if ($status != 'start') $row->resume_date = null;
                $row->status = $status;
                $row->save();
            } else {
                $this->view->error = trlKwf('Unknown status.');
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
