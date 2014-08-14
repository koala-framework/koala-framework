<?php
class Kwf_Controller_Action_User_Users_LockedData extends Kwf_Data_Abstract
{
    public function load($row)
    {
        if (isset($row->locked)) {
            return $row->locked;
        }
        while ($row instanceof Kwf_Model_Proxy_Row) $row = $row->getProxiedRow();
        while ($row instanceof Kwf_Model_Union_Row) $row = $row->getSourceRow();
        if (isset($row->locked)) {
            return $row->locked;
        }
        return false;
    }
}
