<?php
class Kwc_Articles_Detail_Feedback_Form_Controller extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Articles_Directory_FeedbacksModel';
    protected $_buttons = array();

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Datetime('date'));
        $this->_columns->add(new Kwf_Grid_Column('user_email', 'Benutzer', 150));
        $this->_columns->add(new Kwf_Grid_Column('text', 'Text', 500))
            ->setRenderer('nl2br');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('limit'=>1, 'ignoreVisible'=>true));
        $c = $c->getParentByClass('Kwc_Articles_Detail_Component');
        $ret->whereEquals('article_id', $c->id);
        return $ret;
    }
}
