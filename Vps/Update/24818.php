<?php
class Vps_Update_24818 extends Vps_Update
{
    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Db_AddField(array(
            'table' => 'cache_component_meta',
            'field' => 'callback',
            'type' => 'varchar(255)',
            'null' => true,
            'default' => NULL,
        ));
    }
}
