<?php
class Vps_Model_Mongo_Row extends Vps_Model_Row_Data_Abstract
{

    public function __get($name)
    {
        $ret = parent::__get($name);
        if ($ret instanceof MongoDate) {
            $ret = date('Y-m-d H:i:s', $ret->sec);
        }
        return $ret;
    }

}
