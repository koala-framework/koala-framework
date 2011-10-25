<?php
class Kwc_Directories_Category_ShowCategories_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $showDirectoryClass = Kwc_Abstract::getSetting($this->getClass(), 'showDirectoryClass');
        $hideDirectoryClasses = Kwc_Abstract::getSetting($this->getClass(), 'hideDirectoryClasses');

        $cards = $this->add(new Kwf_Form_Container_Cards('source_component_id', trlKwf('Directory')));

        $defaultCard = null;
        $categories = Kwf_Component_Data_Root::getInstance()
                ->getComponentsByClass('Kwc_Directories_Category_Directory_Component');
        foreach ($categories as $category) {
            $itemDirectory = $category->parent;
            if (is_instance_of($itemDirectory->componentClass, $showDirectoryClass)) {
                foreach ($hideDirectoryClasses as $c) {
                    if (is_instance_of($itemDirectory->componentClass, $c)) {
                        continue 2;
                    }
                }
                $categoriesModel = $category->getComponent()->getChildModel();
                $select = $categoriesModel->select()
                    ->whereEquals('component_id', $category->componentId);
                $values = array();
                foreach ($categoriesModel->getRows($select) as $row) {
                    $values[$row->id] = $row->name;
                }

                $card = $cards->add();
                $card->setTitle($category->parent->getTitle());
                $card->setName($category->componentId);
                if (!$defaultCard) $defaultCard = $category->componentId;
                $model = Kwf_Model_Abstract::getInstance('Kwc_Directories_Category_ShowCategories_Model');
                $card->add(new Kwf_Form_Field_MultiCheckboxLegacy($model, trlKwf('Categories')))
                    ->setValues($values)
                    ->setReferences(array(
                        'columns' => array('component_id'),
                        'refColumns' => array('id')
                    ))
                    ->setColumnName('category_id');
            }
        }
        $cards->setDefaultValue($defaultCard);
    }
}
