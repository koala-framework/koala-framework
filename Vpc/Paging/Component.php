<?php
class Vpc_Paging_Component extends Vpc_Abstract
{
    private $_entries;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['pagesize'] = 10;
        $ret['maxPagingLinks'] = 13;
        $ret['bigPagingSteps'] = array(10, 50);
        $ret['cssClass'] = 'webPaging webStandard';
        $ret['viewCache'] = false;
        return $ret;
    }
    public function getCount()
    {
        if (!isset($this->_entries)) {
            $this->_entries = $this->getData()->parent->getComponent()->getPagingCount();
            if (!$this->_entries) {
                $this->_entries = 0;
            } else if ($this->_entries instanceof Vps_Component_Select) {
                $this->_entries = $this->getData()->parent->countChildComponents($this->_entries);
            } else if ($this->_entries instanceof Vps_Model_Select) {
                throw new Vps_Exception("Not yet implemented, probably not really possible");
            } else if ($this->_entries instanceof Zend_Db_Select) {
                $select = $this->_entries;
                $select->setIntegrityCheck(false);
                $select->reset(Zend_Db_Select::COLUMNS);
                if ($select instanceof Vps_Db_Table_Select) {
                    $table = $select->getTableName().'.';
                } else {
                    $table = '';
                }
                $select->from(null, array('count' => "COUNT(DISTINCT {$table}id)"));
                $r = $select->query()->fetchAll();
                if (!isset($r[0])) {
                    $this->_entries  = 0;
                } else {
                    $this->_entries = $r[0]['count'];
                }
                if ($select->getPart(Zend_Db_Select::LIMIT_COUNT)) {
                    //falls select ein limit hat dieses verwenden
                    $this->_entries  = min($this->_entries, $select->getPart(Zend_Db_Select::LIMIT_COUNT));
                }
            }
        }
        return $this->_entries;
    }

    private function _getLinkData($pageNumber, $linktext = null)
    {
        if (is_null($linktext)) $linktext = $pageNumber;

        $params = array();
        foreach ($_GET as $p=>$v) {
            if ($p != $this->_getParamName() && !is_array($v)) {
                $params[] = "$p=".urlencode($v);
            }
        }
        $params = implode('&', $params);

        $currentPage = $this->_getCurrentpage();
        $p = '';
        if ($pageNumber == 1) {
            if ($params) $p = '?'.$params;
        } else {
            $p = '?'.$this->_getParamName().'='.$pageNumber;
            if ($params) $p .= '&'.$params;
        }

        return array(
            'text' => $linktext,
            'href' => $this->getUrl().$p,
            'rel'  => '',
            'active' => $currentPage == $pageNumber
        );
    }

    protected function _getParamName()
    {
        return $this->getDbId();
    }

    protected function _getPages()
    {
        return ceil($this->getCount() / $this->_getSetting('pagesize'));
    }

    protected function _getCurrentPage()
    {
        if (!isset($_GET[$this->_getParamName()])) {
            $page = 1;
        } else {
            $page = (int)$_GET[$this->_getParamName()];
        }
        if ($page < 1) $page = 1;
        return $page;
    }

    public function hasContent()
    {
        return ($this->_getPages() > 1 ? true : false);
    }

    public function getLimit()
    {
        $ret = array();
        $ret['limit'] = $this->_getSetting('pagesize');
        $ret['start'] = ($this->_getCurrentPage()-1)*$this->_getSetting('pagesize');
        return $ret;
    }

    public function limitSelect(Vps_Model_Select $select)
    {
        $limit = $this->getLimit();
        if ($select->hasPart(Vps_Model_Select::LIMIT_COUNT)) {
            //wenn schon ein limit gesetzt
            $existingLimitCount = $select->getPart(Vps_Model_Select::LIMIT_COUNT);
            if ($existingLimitCount < $limit['limit']) {
                return;
            }
        }
        $select->limit($limit);
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['pages'] = $this->_getPages();
        $ret['currentPage'] = $this->_getCurrentpage();
        $ret['pageLinks'] = array();

        if ($ret['currentPage'] >= 3) {
            $ret['pageLinks'][] = $this->_getLinkData(1, '&lt;&lt;');
        }
        if ($ret['currentPage'] >= 2) {
            $ret['pageLinks'][] = $this->_getLinkData($ret['currentPage']-1, '&lt;');
        }

        $appendPagelinks = array();
        $bigSteps = $this->_getSetting('bigPagingSteps');
        rsort($bigSteps);
        foreach ($bigSteps as $stepOffset) {
            if ($this->_getSetting('maxPagingLinks') < $stepOffset * 2) {
                if ($ret['currentPage'] >= $stepOffset + 1) {
                    $ret['pageLinks'][] = $this->_getLinkData($ret['currentPage'] - $stepOffset);
                }

                if ($ret['currentPage'] <= $ret['pages'] - $stepOffset) {
                    array_unshift($appendPagelinks, $this->_getLinkData($ret['currentPage'] + $stepOffset));
                }
            }
        }

        if ($ret['currentPage'] < $ret['pages']) {
            $appendPagelinks[] = $this->_getLinkData($ret['currentPage']+1, '&gt;');
        }
        if ($ret['currentPage'] < $ret['pages'] - 1) {
            $appendPagelinks[] = $this->_getLinkData($ret['pages'], '&gt;&gt;');
        }

        $linksPerDirection = floor(
            ($this->_getSetting('maxPagingLinks') - (count($ret['pageLinks']) + count($appendPagelinks) + 1)) / 2
        );
        if ($linksPerDirection < 0) $linksPerDirection = 0;

        $fromPage = $ret['currentPage'] - $linksPerDirection;
        $toPage = $ret['currentPage'] + $linksPerDirection;
        if ($fromPage < 1) {
            $toPage += abs($fromPage - 1);
        }
        if ($toPage > $ret['pages']) {
            $fromPage -= ($toPage - $ret['pages']);
        }
        if ($fromPage < 1) $fromPage = 1;
        if ($toPage > $ret['pages']) $toPage = $ret['pages'];

        for ($i = $fromPage; $i <= $toPage; $i++) {
            $ret['pageLinks'][] = $this->_getLinkData($i);
        }

        foreach ($appendPagelinks as $linkData) {
            $ret['pageLinks'][] = $linkData;
        }

        return $ret;
    }
}
