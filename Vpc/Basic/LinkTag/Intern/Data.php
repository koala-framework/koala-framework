<?php
class Vpc_Basic_LinkTag_Intern_Data extends Vps_Component_Data
{
    private $_data;

    protected function _getRow()
    {
        $m = Vpc_Abstract::createModel($this->componentClass);
        $row = $m->getRow($this->dbId);
    }

    protected function _getData()
    {
        if ($row = $this->_getRow()) {
            return Vps_Component_Data_Root::getInstance()
                                            ->getComponentByDbId($row->target);
        }
        return false;
    }

    public function __get($var)
    {
        if (!isset($this->_data)) {
            $this->_data = $this->_getData();
        }
        if ($var == 'url') {
            $page = $this->_data;
            if (!$page) return '';
            return $page->url;
        } else if ($var == 'rel') {
            $page = $this->_data;
            if (!$page) return '';
            return $page->rel;
        } else {
            return parent::__get($var);
        }
    }

}
