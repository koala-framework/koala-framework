<?php
class Vps_Form_Field_Abstract_Controller extends Vpc_Formular_Dynamic_Controller
{
    private $_formularId;
    private $_parentComponentId;
    public function preDispatch()
    {
        $this->_model = new Vps_Model_Db(array(
            'table' => new Vpc_Formular_Dynamic_Model()
        ));
        //TODO: recht unschön :D
        if (preg_match('#^(.*)-([0-9]*)$#', $this->componentId, $m)) {
            $this->_parentComponentId = $m[1];
            $this->_formularId = $m[2];
        }
        parent::preDispatch();
    }
    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['parent_id = ?'] = $this->_formularId;
        unset($where['component_id = ?']);
        return $where;
    }

    protected function _preforeAddParagraph($row)
    {
        $row->parent_id = $this->_formularId;
        $row->component_id = $this->_parentComponentId;
    }
    public function init()
    {
        $this->_buttons[] = 'settings';
        parent::init();
    }
}
