<?php
class Vps_Update_23173 extends Vps_Update
{
    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vps_pages',
            'field' => 'tags',
            'type' => 'varchar(100)',
            'null' => false,
            'default' => '',
        ));
    }
}
