<?php
class Kwc_Advanced_GoogleMap_Update_22701 extends Kwf_Update
{
    protected function _init()
    {
        $this->_actions[] = new Kwf_Update_Action_Component_ConvertTableToFieldModel(array(
            'table'=>'kwc_advanced_googlemap',
        ));
        $this->_actions[] = new Kwf_Update_Action_Db_DropTable(array(
            'table'=>'kwc_advanced_googlemap',
        ));
    }

}
