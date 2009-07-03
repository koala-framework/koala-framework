<?php
class Vpc_Newsletter_Detail_MailingController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_modelName = 'Vpc_Newsletter_QueueModel';
    protected $_queryFields = array('searchtext');

    public function preDispatch()
    {
        $this->_editDialog = array(
            'type' => 'Vpc.Newsletter.Detail.MailPanel',
            'controllerUrl' => Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Mail'),
            'baseParams' => array(
                'componentId' => $this->_getParam('componentId')
            ),
            'width' => 300,
            'height' => 300
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

        $this->_columns->add(new Vps_Grid_Column('gender', trlVps('Gender'), 80))
            ->setData(new Vpc_Newsletter_Detail_UserData('gender'))
            ->setRenderer('genderIcon');
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 80))
            ->setData(new Vpc_Newsletter_Detail_UserData('title'));
        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('Firstname'), 140))
            ->setData(new Vpc_Newsletter_Detail_UserData('firstname'));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Lastname'), 140))
            ->setData(new Vpc_Newsletter_Detail_UserData('lastname'));
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('E-Mail'), 200))
            ->setData(new Vpc_Newsletter_Detail_UserData('email'));
        $this->_columns->add(new Vps_Grid_Column('format', trlVps('Format'), 60))
            ->setData(new Vpc_Newsletter_Detail_UserData('format'));
        $this->_columns->add(new Vps_Grid_Column('status', trlVps('Status'), 60));
        $this->_columns->add(new Vps_Grid_Column('sent_date', trlVps('Date Sent'), 80));
        $this->_columns->add(new Vps_Grid_Column_Button('show'))
            ->setButtonIcon(new Vps_Asset('email_open.png'));
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
        $row = $this->_getNewsletterRow();
        $model = Vps_Model_Abstract::getInstance('Vpc_Newsletter_QueueModel');
        $select = $model->select()->whereEquals('newsletter_id', $row->id);
        $count = $model->countRows($select);
        $countSent = $model->countRows($select->whereEquals('status', 'sent'));
        $countQueued = $model->countRows($select->whereEquals('status', 'queued'));
        $countErrors = $count - $countQueued - $countSent;
        switch ($row->status) {
            case 'stop': $text = trlVps('Newsletter stopped, cannot start again.'); break;
            case 'pause': $text = trlVps('Newsletter paused.'); break;
            case 'start': $text = trlVps('Newsletter sending.'); break;
            default: $text = trlVps('Newsletter waiting for start.'); break;
        }
        $info['statusText'] = trlVps(
            '{0} {1} e-mails sent, {2} recipients total. {3} errors.',
            array($text, $countSent, $count, $countErrors)
        );
        $info['state'] = $row->status;
        $this->view->info = $info;
    }
}