<?php
class Vpc_Basic_LinkTag_Intern_Data extends Vps_Component_Data
{
    private $_data;
    private function _getData()
    {
        if (!isset($this->_data)) {
            $m = Vpc_Abstract::createModel($this->componentClass);
            $row = $m->getRow($this->dbId);
            if ($row) {
                $this->_data = Vps_Component_Data_Root::getInstance()
                                                ->getComponentByDbId($row->target);
            } else {
                $this->_data = false;
            }
        }
        return $this->_data;
    }
    public function __get($var)
    {
        if ($var == 'url') {
            $page = $this->_getData();
            if (!$page) return '';
            return $page->url;
        } else if ($var == 'rel') {
            $page = $this->_getData();
            if (!$page) return '';
            return $page->rel;
        } else {
            return parent::__get($var);
        }
    }

}
