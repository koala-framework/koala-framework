<?php
class Kwc_Root_Category_Trl_Update_20151116UpdatePageNames extends Kwf_Update
{
    public function update()
    {
        $root = Kwf_Component_Data_Root::getInstance();
        $model = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_Trl_GeneratorModel');
        $select = $model->select()->where(new Kwf_Model_Select_Expr_Like('filename', '%_%'));
        foreach ($model->getRows($select) as $row) {
            if ($root->getComponentById($row->component_id, array('ignoreVisible' => true))) {
                $row->save();
            }
        }
    }
}
