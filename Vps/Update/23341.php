<?php
class Vps_Update_23341 extends Vps_Update
{
    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'vps_pages',
            'field' => 'domain',
            'type' => 'varchar(100)',
            'null' => true,
            'default' => 'NULL',
        ));
    }
}
