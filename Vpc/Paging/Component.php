<?php
class Vpc_Paging_Component extends Vpc_Abstract
{
    protected $_entries;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['pagesize'] = 10;
        return $ret;
    }

    protected function _getParamName()
    {
        return $this->getDbId();
    }

    public function setEntries($entries)
    {
        $this->_entries = (int)$entries;
    }

    public function getEntries()
    {
        return $this->_entries;
    }

    protected function _getPages()
    {
        return ceil($this->_entries / $this->_getSetting('pagesize'));
    }

    protected function _getCurrentPage()
    {
        $pages = $this->_getPages();
        if (!isset($_GET[$this->_getParamName()])) {
            $page = 1;
        } else {
            $page = (int)$_GET[$this->_getParamName()];
        }
        if ($page < 1 || $page > $pages) $page = 1;
        return $page;
    }
    
    public function getLimit()
    {
        $ret = array();
        $ret['limit'] = $this->_getSetting('pagesize');
        $ret['start'] = ($this->_getCurrentPage()-1)*$this->_getSetting('pagesize');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['pages'] = $this->_getPages();
        $ret['currentPage'] = $this->_getCurrentpage();
        $ret['pageLinks'] = array();
        for ($i = 1; $i <= $ret['pages']; $i++) {
            $ret['pageLinks'][] = array(
                'text' => $i,
                'href' => $this->getUrl().'?'.$this->_getParamName().'='.$i,
                'rel'  => '',
                'active' => $ret['currentPage']==$i
            );
        }
        return $ret;
    }
}
