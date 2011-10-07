<?php
class Kwc_Directories_Category_Directory_ItemEditForm extends Kwf_Form
{
    public function __construct($name, $class, $dbId)
    {
        parent::__construct($name);

        $modelName = Kwc_Abstract::getSetting($class, 'categoryToItemModelName');
        $model = Kwf_Model_Abstract::getInstance($modelName);
        $relToItemRef = $model->getReference('Item');

        if (isset($relToItemRef['refModel'])) {
            $itemModel = $relToItemRef['refModel'];
        } else {
            $itemModel = Kwf_Model_Abstract::getInstance($relToItemRef['refModelClass']);
        }

        $this->setModel($itemModel);
        $this->setIdTemplate('{0}');
        $this->setCreateMissingRow(true);

        $s = new Kwf_Model_Select();
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($dbId, array('ignoreVisible'=>true));
        $c = $c->getChildComponent(array('componentClass'=>$class, 'ignoreVisible'=>true));
        $s->whereEquals('component_id', $c->dbId);
        $this->add(new Kwf_Form_Field_MultiCheckbox($model, 'Category', trlKwf('Categories')))
            ->setValuesSelect($s);
    }
}
