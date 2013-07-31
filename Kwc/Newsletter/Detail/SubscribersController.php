<?php
class Kwc_Newsletter_Detail_SubscribersController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_paging = 10;

    public function init()
    {
        $this->setModel($this->_getParam('subscribeModel'));
        parent::init();
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $mailComponent = $this->_getMailComponent();
        $rs = $mailComponent->getComponent()->getRecipientSources();
        foreach(array_keys($rs) as $key) {
            if (isset($rs[$key]['select']) && ($rs[$key]['model'] == get_class($this->_getModel()))) {
                $ret->merge($rs[$key]['select']);
            }
        }
        return $ret;
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $columns = $this->_columns;
        $columns->add(new Kwf_Grid_Column('id'));
        $columns->add(new Kwf_Grid_Column('firstname'));
        $columns->add(new Kwf_Grid_Column('lastname'));
        $columns->add(new Kwf_Grid_Column('email'));
    }

    public function jsonGetRecipientSourcesAction()
    {
        $mailComponent = $this->_getMailComponent();
        $rs = $mailComponent->getComponent()->getRecipientSources();
        foreach($rs as $key => $value) {
            if (isset($rs[$key]['title']))
                $rs[$key]['title'] = $mailComponent->trlStaticExecute($rs[$key]['title']);
        }
        $this->view->sources = $rs;

        $rs = reset($rs);
        if (!isset($rs['select'])) $rs['select'] = array();
        $row = Kwf_Model_Abstract::getInstance($rs['model'])->getRow($rs['select']);
        $this->view->subscribeModel = $rs['model'];
        $this->view->recipientId = $row->id;
    }

    protected function _getMailComponent()
    {
        $mailComponent = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId') . '-mail',
            array('ignoreVisible' => true)
        );
        return $mailComponent;
    }
}
