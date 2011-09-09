<?php
class Vpc_Basic_LinkTag_Intern_Data extends Vps_Component_Data
{
    private $_data;

    protected function _getData()
    {
        $m = Vpc_Abstract::createModel($this->componentClass);
        if ($m->getProxyModel() instanceof Vps_Model_Db) {
            //performance, avoid model overhead
            $sql = "SELECT target FROM ".$m->getProxyModel()->getTableName()." WHERE component_id=?";
            $target = Vps_Registry::get('db')->query($sql, $this->dbId)->fetchColumn();
        } else {
            $row = $m->getRow($this->dbId);
            $target = $row ? $row->target : false;
        }
        if ($target) {
            $ret = null;
            $components = Vps_Component_Data_Root::getInstance()->getComponentsByDbId(
                $target,
                array('subroot' => $this, 'limit' => 1)
            );
            if ($components) $ret = $components[0];
            if (!$ret) {
                $ret = Vps_Component_Data_Root::getInstance()->getComponentByDbId(
                    $target
                );
            }
            return $ret;
        }
        return false;
    }

    public final function getLinkedData()
    {
        if (!isset($this->_data)) {
            $this->_data = $this->_getData();
            if (!$this->_data) $this->_data = false;
        }
        return $this->_data;
    }

    public function __get($var)
    {
        if ($var == 'url') {
            if (!$this->getLinkedData()) return '';
            return $this->getLinkedData()->url;
        } else if ($var == 'rel') {
            if (!$this->getLinkedData()) return '';
            return $this->getLinkedData()->rel;
        } else {
            return parent::__get($var);
        }
    }

}
