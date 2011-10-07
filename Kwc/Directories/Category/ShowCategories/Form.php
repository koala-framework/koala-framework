<?php
class Vpc_Directories_Category_ShowCategories_Form extends Vpc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $showDirectoryClass = Vpc_Abstract::getSetting($this->getClass(), 'showDirectoryClass');
        $hideDirectoryClasses = Vpc_Abstract::getSetting($this->getClass(), 'hideDirectoryClasses');

        $cards = $this->add(new Vps_Form_Container_Cards('source_component_id', trlVps('Directory')));

        $defaultCard = null;
        $categories = Vps_Component_Data_Root::getInstance()
                ->getComponentsByClass('Vpc_Directories_Category_Directory_Component');
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
                $model = Vps_Model_Abstract::getInstance('Vpc_Directories_Category_ShowCategories_Model');
                $card->add(new Vps_Form_Field_MultiCheckboxLegacy($model, trlVps('Categories')))
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
