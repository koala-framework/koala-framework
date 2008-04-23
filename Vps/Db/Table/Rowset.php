<?php
class Vps_Db_Table_Rowset extends Vps_Db_Table_Rowset_Abstract
{
    public function toDebug()
    {
        $i = get_class($this);
        $ret = print_r($this->_data, true);
        $ret = preg_replace('#^Array#', $i, $ret);
        $ret = "<pre>$ret</pre>";
        return $ret;
    }
}
