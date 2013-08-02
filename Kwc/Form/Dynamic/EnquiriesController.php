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
        foreach ($c->getChildComponent('-paragraphs')->getRecursiveChildComponents(array('flags'=>array('formField'=>true))) as $c) {
            $f = $c->getComponent()->getFormField();
            if ($f instanceof Kwf_Form_Field_SimpleAbstract) {
                $this->_columns->add(new Kwf_Grid_Column($f->getFieldName(), $f->getFieldLabel()))
                    ->setSortable(false);
            }
        }

    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }
}
