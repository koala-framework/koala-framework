<?php
class Kwc_Advanced_GoogleMap_Update_20150309Legacy37684 extends Kwf_Update
{
    public function update()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_FieldModel');
        $select = $model->select()->where(
            new Kwf_Model_Select_Expr_Like('data', '%coordinates%')
        );
        foreach ($model->getRows($select) as $row) {
            if (isset($row->coordinates) && $row->coordinates) {
                $row->routing = 1;
                $row->save();
            }
        }
    }
}
