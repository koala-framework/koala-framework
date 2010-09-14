<?php
class Vps_Model_Row_Data_Abstract extends Vps_Model_Row_Abstract
{
    protected $_data = array();
    protected $_cleanData = array();

    public function __construct(array $config)
    {
        $this->_data = (array)$config['data'];
        $this->_cleanData = $this->_data;
        parent::__construct($config);
    }

    //internal
    public function setData($data)
    {
        foreach (array_keys($this->_cleanData) as $k) {
            if (!isset($this->_data[$k]) || $this->_cleanData[$k] === $this->_data[$k]) {
                //nicht geändert
                if (isset($data[$k])) {
                    $this->_data[$k] = $data[$k];
                } else {
                    unset($this->_data[$k]);
                }
            }
        }
        $this->_cleanData = $data;
    }

    public function serialize()
    {
        return serialize(array(
            'parent' => parent::serialize(),
            'data' => $this->_data
        ));
    }
    public function unserialize($str)
    {
        $data = unserialize($str);
        $this->_data = $data['data'];
        $this->_cleanData = $this->_data;
        parent::unserialize($data['parent']);
    }

    public function __unset($name)
    {
        if ($this->_model->getOwnColumns() && !in_array($name, $this->_model->getOwnColumns())) {
            parent::__unset($name);
        } else if (isset($this->_data[$name])) {
            $name = $this->_transformColumnName($name);
            unset($this->_data[$name]);
        }
    }

    public function __get($name)
    {
        if ($this->_model->getOwnColumns() && !in_array($name, $this->_model->getOwnColumns())) {
            return parent::__get($name);
        } else {
            $name = $this->_transformColumnName($name);
            if (!isset($this->_data[$name])) return null;
            return $this->_data[$name];
        }
    }

    public function __set($name, $value)
    {
        if ($this->_model->getOwnColumns() && !in_array($name, $this->_model->getOwnColumns())) {
            parent::__set($name, $value);
            return;
        }
        $n = $this->_transformColumnName($name);
        if ($this->$name !== $value) {
            $this->_setDirty();
        }
        $this->_data[$n] = $value;
        $this->_postSet($name, $value);
    }

    public function toArray()
    {
        $ret = parent::toArray();
        foreach ($this->_model->getOwnColumns() as $c) {
            $ret[$c] = $this->$c;
        }
        if (!$this->_model->getOwnColumns()) {
            $ret = array_merge($this->_data, $ret);
        }
        return $ret;
    }

    public function save()
    {
        $update = isset($this->_cleanData[$this->_getPrimaryKey()]);

        $this->_beforeSaveSiblingMaster();
        $this->_beforeSave();
        if ($update) {
            $this->_beforeUpdate();
        } else {
            $this->_beforeInsert();
        }

        if ($update) {
            if ($this->_isDirty()) {
                $ret = $this->_model->update($this, $this->_data);
                $this->_setDirty(false);
            } else {
                $ret = $this->{$this->_getPrimaryKey()};
            }
        } else {
            $ret = $this->_model->insert($this, $this->_data);
            $this->_data[$this->_getPrimaryKey()] = $ret;
            $this->_setDirty(false);
        }
        $this->_cleanData = $this->_data;

        if ($update) {
            $this->_afterUpdate();
        } else {
            $this->_afterInsert();
        }
        $this->_afterSave();
        parent::save(); //siblings nach uns speichern; damit auto-inc id vorhanden

        return $ret;
    }

    public function delete()
    {
        parent::delete();

        $this->_beforeDelete();
        $id = $this->{$this->_getPrimaryKey()};
        $this->_model->delete($this);
        $this->_afterDelete();

        $this->_data = array_combine(
            array_keys($this->_data),
            array_fill(0, count($this->_data), null)
        );
    }

}
