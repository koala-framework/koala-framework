<?php
class Vps_Model_Mongo_Row extends Vps_Model_Row_Data_Abstract
{
    public function __set($name, $value)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            //TODO: expr column setzen nicht erlauben
            //ist im moment aber noch nötig wegen updaten der expr werte
            $n = $this->_transformColumnName($name);
            if ($this->$name !== $value) {
                $this->_setDirty(true);
            }
            $this->_data[$n] = $value;
            $this->_postSet($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }

    public function __get($name)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            $name = $this->_transformColumnName($name);
            if (!isset($this->_data[$name])) {
                $ret = null;
            } else {
                $ret = $this->_data[$name];
            }
        } else {
            $ret = parent::__get($name);
        }
        if ($ret instanceof MongoDate) {
            $ret = date('Y-m-d H:i:s', $ret->sec);
        }
        return $ret;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        foreach ($this->getModel()->getExprColumns() as $name) {
            $this->$name = $this->getModel()->getExprValue($this, $name);
        }
        foreach ($this->getModel()->getProxyContainerModels() as $model) {
            foreach ($model->getExprColumns() as $name) {
                foreach ($model->getExistingRows() as $proxyRow) {
                    if ($proxyRow->getProxiedRow() === $this) {
                        $this->$name = $model->getExprValue($proxyRow, $name);
                    }
                }
            }
        }
    }
}
