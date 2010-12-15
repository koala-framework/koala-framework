<?php
class Vps_Util_Model_Row_Country extends Vps_Model_Row_Data_Abstract
{
    public function __get($name)
    {
        if (preg_match('#^name_([a-z]{2})$#', $name, $m)) {
            $ret = $this->getModel()->getNameByLanguageAndId($m[1], $this->id);
        } else {
            $ret = parent::__get($name);
        }
        return $ret;
    }

}
