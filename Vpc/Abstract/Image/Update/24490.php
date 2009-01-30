<?php
class Vpc_Abstract_Image_Update_24490 extends Vps_Update
{
    protected function _init()
    {
        parent::_init();
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vpc_basic_image',
            'field' => 'dimension',
            'type' => 'varchar(200)',
            'null' => true
        ));
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vpc_basic_image',
            'field' => 'data',
            'type' => 'text',
            'default'=>'',
            'null' => false
        ));
        //TODO: comment feld entfernen (vorher konvertieren?)
    }
}
