<?php
class Vpc_Basic_LinkTag_Intern_Data extends Vps_Component_Data
{
    private $_data;

    protected function _getRow()
    {
        $m = Vpc_Abstract::createModel($this->componentClass);
        return $m->getRow($this->dbId);
    }

    protected function _getData()
    {
        if ($row = $this->_getRow()) {
            return Vps_Component_Data_Root::getInstance()->getComponentByDbId(
                $row->target,
                array('subroot' => $this)
            );
        }
        return false;
    }

    public function __get($var)
    {
        if (!isset($this->_data)) {
            $this->_data = $this->_getData();
            if (!$this->_data) $this->_data = false;
        }
        if ($var == 'url') {
            if (!$this->_data) return '';
            return $this->_data->url;
        } else if ($var == 'rel') {
            if (!$this->_data) return '';
            return $this->_data->rel;
        } else {
            return parent::__get($var);
        }
    }

}
