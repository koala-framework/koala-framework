<?php
class Kwc_Feedback_Form_Controller extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Feedback_Model';
    protected $_buttons = array();

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Datetime('date'));
        $this->_columns->add(new Kwf_Grid_Column('email', 'Benutzer', 150))
            ->setData(new Kwc_Feedback_Form_ControllerData());
        $this->_columns->add(new Kwf_Grid_Column('text', 'Text', 500))
            ->setRenderer('nl2br');
    }

    // shows all feedbacks in all languages
    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $componentId = $this->_getParam('componentId');
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($componentId, array('ignoreVisible' => true));
        if (isset($component->parent->chained)) {
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

class Kwc_Feedback_Form_ControllerData extends Kwf_Data_Abstract
{
    public function load($row) {
        return $row->getParentRow('User')->email;
    }
}
