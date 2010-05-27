<?php
class Vpc_Directories_Category_Directory_ItemEditForm extends Vps_Form
{
    public function __construct($name, $class, $dbId)
    {
        parent::__construct($name);

        $modelName = Vpc_Abstract::getSetting($class, 'categoryToItemModelName');
        $model = Vps_Model_Abstract::getInstance($modelName);
        $relToItemRef = $model->getReference('Item');

        if (isset($relToItemRef['refModel'])) {
            $itemModel = $relToItemRef['refModel'];
        } else {
            $itemModel = Vps_Model_Abstract::getInstance($relToItemRef['refModelClass']);
        }

        $this->setModel($itemModel);
        $this->setIdTemplate('{0}');
        $this->setCreateMissingRow(true);

        $s = new Vps_Model_Select();
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($dbId, array('ignoreVisible'=>true));
        $c = $c->getChildComponent(array('componentClass'=>$class, 'ignoreVisible'=>true));
        $s->whereEquals('component_id', $c->dbId);
        $this->add(new Vps_Form_Field_MultiCheckbox($model, 'Category', trlVps('Categories')))
            ->setValuesSelect($s);
    }
}
