<?php
class Kwc_Directories_CategorySimple_List_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $values = array();
        $model = Kwf_Model_Abstract::getInstance('Kwc_Directories_CategorySimple_CategoriesModel');
        foreach ($model->getRootNodes($model->select()->order('pos')) as $node) {
            foreach ($node->getChildNodes($model->select()->order('pos')) as $child) {
                $values[$child->id] = $node->name . " -> " . $child->name;
            }
        }
        $this->add(new Kwf_Form_Field_Select('category_id', trl('Kategorie')))
            ->setValues($values)
            ->setAllowBlank(false)
            ->setWidth(300);
    }
}
