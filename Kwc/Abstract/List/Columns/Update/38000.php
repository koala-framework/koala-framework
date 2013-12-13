<?php
class Kwc_Abstract_List_Columns_Update_38000 extends Kwf_Update
{
    public function update()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_FieldModel');
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Like('data', '%variant%'));
        $variants = array();
        foreach ($model->getRows($select) as $row) {
            $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible'=>true, 'limit'=>1));
            if ($component && is_instance_of($component->componentClass, 'Kwc_Abstract_List_Columns_Component')) {
                $cRow = $component->getComponent()->getRow();
                $cRow->columns = substr($row->variant, 0, 1);
                if (!in_array($cRow->variant, $variants)) {
                    echo "$cRow->variant -> $cRow->columns\n";
                    $variants[] = $cRow->variant;
                }
                $cRow->save();
            }
        }
    }
}
