<?php
class Kwc_Newsletter_Detail_PreviewController extends Kwc_Mail_PreviewController
{
    protected $_model = 'Kwc_Newsletter_Subscribe_Model';
    protected function _getRecipient()
    {
        $model = Kwf_Model_Abstract::getInstance($this->_model);
        if (!$this->_getParam('recipientId')) {
            $params = array(
                'title' => 'Prof.',
                'gender' => 'male',
                'firstname' => 'Max',
                'lastname' => 'Mustermann',
                'email' => 'max@mustermann.com'
            );
            $row = $model->createRow($params);
        } else {
            $select = new Kwf_Model_Select();
            $select->whereEquals('id', $this->_getParam('recipientId'));
            $row = $model->getRow($select);
        }
        return $row;
    }

    protected function _getMailComponent()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId') . '-mail', 
            array('ignoreVisible' => true)
        );
        return $component->getComponent();
    }

    public function jsonGetRecipientsAction()
    {
        $componentId = str_replace(strrchr($this->_getParam('componentId'), '_'), '', $this->_getParam('componentId'));
        $model = Kwf_Model_Abstract::getInstance($this->_model);
        $select = new Kwf_Model_Select();
        $select->whereEquals('newsletter_component_id', $componentId);
        if ($query = $this->_getParam('query')) {
            $select->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Like('firstname', '%'.$query.'%'),
                new Kwf_Model_Select_Expr_Like('lastname', '%'.$query.'%')
            )));
        }
        $this->view->total = $model->countRows($select);
        if ($limit = $this->_getParam('limit')) {
            $select->limit($limit, $this->_getParam('start'));
        }
        $recipients = array();
        foreach($model->getRows($select) as $row) {
            $recipients[] = array(
                'id' => $row->id,
                'name' => $row->firstname . ' ' . $row->lastname
            );
        }
        $this->view->recipients = $recipients;
    }
}
