<?php
class Kwc_Directories_CategorySimple_List_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $values = array();
        $model = Kwf_Model_Abstract::getInstance('Kwc_Directories_CategorySimple_CategoriesModel');
        $rows = $model->getRows();
        $values = array();
        foreach($rows as $row){
            $val = array();
            $val['id'] = $row->id;
            $val['value'] = $row->getTreePath();
            $values[] = $val;
        }
        $this->add(new Kwf_Form_Field_Select('category_id', trlKwf('Category')))
            ->setValues($values)
            ->setAllowBlank(false)
            ->setWidth(400);
    }
}
