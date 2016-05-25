<?php
class Kwc_Root_Category_Update_20151110UpdatePageNames extends Kwf_Update
{
    public function postUpdate()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_GeneratorModel');
        $select = $model->select()->where(new Kwf_Model_Select_Expr_Like('filename', '%_%'));
        foreach ($model->getRows($select) as $row) {
            $page = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($row->id, array('ignoreVisible' => true));
            if ($page) {
                $row->save();
            }
        }
    }
}
