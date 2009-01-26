<?php
class Vps_Update_24420 extends Vps_Update
{
    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vps_pages',
            'field' => 'custom_filename',
            'type' => 'tinyint',
            'null' => false,
            'default' => '0',
        ));
    }
}
