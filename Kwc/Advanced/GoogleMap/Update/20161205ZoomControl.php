<?php
class Kwc_Advanced_GoogleMap_Update_20161205ZoomControl extends Kwf_Update
{
    public function postClearCache()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_FieldModel');
        $select = $model->select()->where(
            new Kwf_Model_Select_Expr_Like('data', '%coordinates%')
        );
        foreach ($model->getRows($select) as $row) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('limit'=>1, 'ignoreVisible'=>true));
            if ($c && is_instance_of('Kwc_Advanced_GoogleMap_Component', $c->componentClass)) {
                if ($row->zoom_properties == 0 || $row->zoom_properties == 1) {
                    $row->zoom_control = true;
                } else {
                    $row->zoom_control = false;
                }
                $row->save();
            }
        }
    }
}
