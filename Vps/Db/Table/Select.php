<?php
class Vps_Db_Table_Select extends Zend_Db_Table_Select
{
    public function where($cond, $value = null, $type = null)
    {
        if (is_array($cond)) {
            foreach ($cond as $key => $val) {
                // is $key an int?
                if (is_int($key)) {
                    // $val is the full condition
                    $this->where($val);
                } else {
                    // $key is the condition with placeholder,
                    // and $val is quoted into the condition
                    $this->where($key, $val);
                }
            }
            return $this;
        } else {
            return parent::where($cond, $value, $type);
        }
    }
    public function limit($count = null, $offset = null)
    {
        if (is_array($count)) {
            $offset = $count['start'];
            $count = $count['limit'];
        }
        return parent::limit($count, $offset);
    }

    public function getTableName()
    {
        return $this->_info['name'];
    }

    public function info()
    {
        return $this->_info;
    }
}
