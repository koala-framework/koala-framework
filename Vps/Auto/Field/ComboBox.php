<?php
class Vps_Auto_Field_ComboBox extends Vps_Auto_Field_SimpleAbstract
{
    public function __construct($field_name = null)
    {
        if ($field_name) $this->setProperty('hiddenName', $field_name);
        parent::__construct(null);
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        if (isset($ret[0]['storeUrl'])) {
            $ret[0]['store'] = array('url' => $ret[0]['storeUrl']);
        }
        return $ret;
    }

    public function setData($data)
    {
        if (is_string($data)) {
            return $this->setStore(array('url' => $data));
        } else if ($data instanceof Vps_Db_Table_Rowset) {
            $data = $data->toStringDataArray();
            return $this->setStore(array('data' => $data));
        } else if (is_array($data)) {
            $d = array();
            foreach ($data as $k=>$i) {
                if (!is_array($i)) {
                    $d[] = array($k, $i);
                } else {
                    $d[] = $i;
                }
            }
            return $this->setStore(array('data' => $d));
        }
    }

//deprecated
    public function setStoreUrl($url)
    {
        return $this->setData($url);
    }

//deprecated
    public function setStoreRowset(Vps_Db_Table_Rowset $rowset)
    {
        return $this->setData($rowset);
    }

//deprecated
    public function setStoreData($data)
    {
        return $this->setData($data);
    }
}
