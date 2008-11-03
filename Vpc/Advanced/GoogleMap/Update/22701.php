<?php
class Vpc_Advanced_GoogleMap_Update_22701 extends Vps_Update
{
    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Component_ConvertTableToFieldModel(array(
            'table'=>'vpc_advanced_googlemap',
        ));
        $this->_actions[] = new Vps_Update_Action_Db_DropTable(array(
            'table'=>'vpc_advanced_googlemap',
        ));
    }

}
