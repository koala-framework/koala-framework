<?php
class Vps_Controller_Action_Enquiries_EnquiriesController
    extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_defaultOrder = array('field'=>'id', 'direction'=>'DESC');
    protected $_paging = 25;
    protected $_model = 'Vps_Model_Mail';

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'vps.enquiries.index';
        $this->view->formControllerUrl = '/vps/enquiries/enquiry';
    }

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Grid_Column('save_date', trlVps('Date'), 110))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Vps_Grid_Column('subject', trlVps('Betreff'), 230));
        $this->_columns->add(new Vps_Grid_Column('from_mail_data', trlVps('From'), 180))
            ->setData(new Vps_Controller_Action_Enquiries_EnquiryFromData());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('is_spam', 0);
        return $ret;
    }

    public function getEnquiryAction()
    {
        $id = $this->_getParam('id');
        $row = $this->_getModel()->getRow($id);

        $view = new Vps_View();
        $view->mailContent = $row->getMailContent();
        $view->subject = $row->subject;
        $view->send_date = $row->save_date;
        $view->cc = $row->getCc();
        $view->header = $row->getHeader();
        $view->bcc = $row->getBcc();
        $view->to = $row->getTo();
        $view->from = $row->getFrom();
        echo $view->render('enquiry.tpl');

        $this->_helper->viewRenderer->setNoRender();
    }
}
