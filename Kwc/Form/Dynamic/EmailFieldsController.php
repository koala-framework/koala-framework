<?php
class Kwc_Form_Dynamic_EmailFieldsController extends Kwf_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId'),
            array('limit'=>1, 'ignoreVisible'=>true)
        );
        $c = $c->getChildComponent('-paragraphs');
        $data = array();
        $formFields = $c->getRecursiveChildComponents(
            array('flags'=>array('formField'=>true), 'ignoreVisible'=>true)
        );
        foreach ($formFields as $f) {
            if (is_instance_of($f->componentClass, 'Kwc_Form_Field_TextField_Component')) {
                $row = $f->getComponent()->getRow();
                if ($row->vtype == 'email') {
                    $data[] = array(
                        'id' => $f->dbId,
                        'name' => $row->field_label
                    );
                }
            }
        }
        $this->_model = new Kwf_Model_FnF(array(
            'data' => $data
        ));
        $this->_columns->add(new Kwf_Grid_Column('id'));
        $this->_columns->add(new Kwf_Grid_Column('name'));
    }
}
