<?php
class Vps_Update_23343 extends Vps_Update
{
    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Db_ChangeField(array(
            'table' => 'vps_pages',
            'field' => 'domain',
            'default' => null
        ));
    }

    public function postUpdate()
    {
        parent::postUpdate();
        Zend_Registry::get('db')->query("UPDATE vps_pages SET domain=NULL WHERE domain='NULL'");
    }
}
