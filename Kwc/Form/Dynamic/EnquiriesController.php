<?php
class Kwc_Form_Dynamic_EnquiriesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_paging = 25;
    protected $_buttons = array('xls');
    protected $_defaultOrder = array(
        'field' => 'save_date',
        'direction' => 'DESC'
    );
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_model = new Kwf_Model_Mail(array(
            'componentClass' => $this->_getParam('class'),
            'mailerClass' => 'Kwf_Mail'
        ));

        $this->_columns->add(new Kwf_Grid_Column('id', trlKwf('Number'), 50));
        $this->_columns->add(new Kwf_Grid_Column_Datetime('save_date', trlKwf('Date')));
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
        $columns = array();
        foreach ($c->getChildComponent('-paragraphs')->getRecursiveChildComponents(array('flags'=>array('formField'=>true))) as $c) {
            $f = $c->getComponent()->getFormField();
            $columns = array_merge($columns, $this->_getColumnsFromField($f));
        }
        foreach ($columns as $column) {
            $this->_columns->add($column);
        }
    }

    protected function _getColumnsFromField($f, $containerName = false)
    {
        $ret = array();
        $f->trlStaticExecute();
        $columnHeader = $containerName ? $containerName . ' - '.$f->getFieldLabel() : $f->getFieldLabel();
        if ($f instanceof Kwf_Form_Field_SimpleAbstract) {
            $c = new Kwf_Grid_Column($f->getFieldName(), $columnHeader);
            $c->setSortable(false);
            $ret[] = $c;

        } else if ($f instanceof Kwf_Form_Container_Abstract) {
            foreach ($f as $subField) {
                $ret = array_merge($ret, $this->_getColumnsFromField($subField, $columnHeader));
            }
        }
        return $ret;
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }
}
