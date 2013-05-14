<?php
class Kwc_Directories_CategorySimple_ItemEditForm extends Kwf_Form
{
    public function __construct($name, $class, $dbId)
    {
        parent::__construct($name);

        $model = Kwf_Model_Abstract::getInstance(
            Kwc_Abstract::getSetting($class, 'categoryToItemModelName')
        );
        $this->setModel($model->getReferencedModel('Item'));
        $this->setIdTemplate('{0}');
        $this->setCreateMissingRow(true);
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Categories')));

        $categoryModel = $model->getReferencedModel('Category');
        $select = $categoryModel->select()
            ->whereEquals('component_id', $dbId)
            ->whereNull('parent_id')
            ->order('pos');
        $cols = $fs->add(new Kwf_Form_Container_Columns());
        foreach ($categoryModel->getRows($select) as $category) {
            $col = $cols->add();
            $s = new Kwf_Model_Select();
            $s->whereEquals('parent_id', $category->id);
            $col->add(new Kwf_Form_Field_MultiCheckbox($model, 'Category', $category->name))
                ->setShowCheckAllLinks(false)
                ->setValuesSelect($s);
        }
    }
}
