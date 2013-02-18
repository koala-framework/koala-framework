<?php
class Kwc_Feedback_Form_Controller extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Feedback_Model';
    protected $_buttons = array();

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Datetime('date'));
        $this->_columns->add(new Kwf_Grid_Column('user_email', 'Benutzer', 150));
        $this->_columns->add(new Kwf_Grid_Column('text', 'Text', 500))
            ->setRenderer('nl2br');
    }

    // shows all feedbacks in all languages
    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $componentId = $this->_getParam('componentId');
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($componentId);
        if(isset($component->parent->chained)) {
            $component = $component->parent->chained;
        }
        $dbIds = array($component->dbId);
        foreach (Kwc_Chained_Abstract_Component::getAllChainedByMaster($component, 'Trl') as $c) {
            $dbIds[] = $c->dbId;
        }
        $ret->whereEquals('component_id', $dbIds);
        return $ret;
    }
}
