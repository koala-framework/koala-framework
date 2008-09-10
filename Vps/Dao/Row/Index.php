<?php
class Vps_Dao_Row_Index extends Vps_Db_Table_Row_Abstract
{
    //Vps_Db_Table_Row_Abstract::_post* nicht aufrufen da wir sonst eine
    //endlosschleife bekommen (cache updaten usw)
    protected function _postUpdate()
    {
        Zend_Db_Table_Row_Abstract::_postUpdate();
    }

    protected function _postInsert()
    {
        Zend_Db_Table_Row_Abstract::_postInsert();
    }

    protected function _postDelete()
    {
        Zend_Db_Table_Row_Abstract::_postDelete();
    }
}
