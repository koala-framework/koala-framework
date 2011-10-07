<?php
class Kwc_Abstract_Image_Update_24472 extends Kwf_Update
{
    protected function _init()
    {
        parent::_init();
        $this->_actions[] = new Kwf_Update_Action_Db_AddField(array(
            'table' => 'kwc_basic_image',
            'field' => 'dimension',
            'type' => 'varchar(200)',
            'null' => true
        ));
        $this->_actions[] = new Kwf_Update_Action_Db_AddField(array(
            'table' => 'kwc_basic_image',
            'field' => 'data',
            'type' => 'text',
            'default'=>'',
            'null' => false
        ));
        //TODO: comment feld entfernen (vorher konvertieren?)
    }
}
