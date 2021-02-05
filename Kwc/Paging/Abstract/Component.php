<?php
class Kwc_Paging_Abstract_Component extends Kwc_Abstract
    implements Kwf_Component_Partial_Interface
{
    private $_entries;
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['pagesize'] = 10;
        $ret['maxPagingLinks'] = 13;
        $ret['bigPagingSteps'] = array(10, 50);
        $ret['nextPrevOnly'] = false;

        // if one of the following is set to false, the link won't be output:
        // first, previous, next, last
        $ret['placeholder'] = array(
            'first'    => '&laquo;',
            'previous' => '&#x8B;',
            'next'     => '&#x9B;',
            'last'     => '&raquo;',
            'prefix'   => trlKwfStatic('Page').':'
        );
        $ret['rootElementClass'] = 'kwfUp-webPaging kwfUp-webStandard';
        $ret['plugins']['useViewCache'] = 'Kwc_Paging_Abstract_UseViewCachePlugin';

        return $ret;
    }

    public static function getPartialClass($componentClass)
    {
        return 'Kwf_Component_Partial_Pager';
    }

    public function getCount()
    {
        if (!isset($this->_entries)) {
            $this->_entries = $this->getData()->parent->getComponent()->getPagingCount();
            if (!$this->_entries) {
                $this->_entries = 0;
            } else if ($this->_entries instanceof Kwf_Component_Select) {
                $this->_entries = $this->getData()->parent->countChildComponents($this->_entries);
            } else if ($this->_entries instanceof Kwf_Model_Select) {
                throw new Kwf_Exception("Not yet implemented, probably not really possible");
            } else if ($this->_entries instanceof Zend_Db_Select) {
                $select = $this->_entries;
                $select->setIntegrityCheck(false);
                $select->reset(Zend_Db_Select::COLUMNS);
                if ($select instanceof Kwf_Db_Table_Select) {
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

    private function _getLinkData($pageNumber, $type = null)
    {
        if (is_null($type)) {
            $text = $pageNumber;
        } else {
            $buttonTexts = $this->_getPlaceholder();
            $text = $buttonTexts[$type];
        }
        if ($text === false) return null;

        $params = array();
        $get = array();
        foreach ($_GET as $p=>$v) {
            if ($p != $this->_getParamName() && !is_array($v)) {
                $params[] = "$p=".urlencode($v);
                $get[$p] = $v;
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
            $get[$this->_getParamName()] = $pageNumber;
        }

        $classes = array();
        if ($currentPage == $pageNumber) $classes[] = 'active';

        $buttonTexts = $this->_getPlaceholder();
        if ($type=='next') {
            $classes[] = 'jumpNext';
        } else if ($type=='previous') {
            $classes[] = 'jumpPrevious';
        } else if ($type=='first') {
            $classes[] = 'jumpFirst';
        } else if ($type=='last') {
            $classes[] = 'jumpLast';
        }

        $linktext = '<span';
        if (!is_numeric($text)) $linktext .= ' class="navigation"';
        $linktext .= '>';
        $linktext .= $text;
        $linktext .= '</span>';

        return array(
            // Für alte Version
            'text' => $text,
            'href' => $this->getUrl().$p,
            'rel'  => '',
            'active' => $currentPage == $pageNumber,
            // ab hier für componentLinkHelper
            'get' => $get,
            'class' => $classes,
            'linktext' => $linktext,
            'currentPageNumber' => $currentPage,
            'pageNumber' => $pageNumber
        );
    }

    protected function _getParamName()
    {
        return $this->getDbId();
    }

    protected function _getPages()
    {
        return ceil($this->getCount() / $this->_getPageSize());
    }

    protected function _getCurrentPage()
    {
        return self::getCurrentPageByParam($this->_getParamName());
    }

    public function getCurrentPage()
    {
        return $this->_getCurrentPage();
    }

    public static function getCurrentPageByParam($paramName)
    {
        if (!isset($_GET[$paramName])) {
            $page = 1;
        } else {
            $page = (int)$_GET[$paramName];
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
        $ret['limit'] = $this->_getPageSize();
        $ret['start'] = ($this->_getCurrentPage()-1)*$this->_getPageSize();
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['pages'] = $this->_getPages();
        $ret['currentPage'] = $this->_getCurrentpage();
        $ret['show'] = $this->getCount() > $this->_getPageSize();
        $ret['partialParams'] = $this->getPartialParams();
        return $ret;
    }

    protected function _getPageSize()
    {
        return $this->_getSetting('pagesize');
    }

    public function getPartialParams($select = null)
    {
        $pagesize = $this->_getPageSize();
        if ($select && $select->hasPart('limitCount') &&
            $select->getPart('limitCount') <= $pagesize
        ) {
            $pagesize = $select->getPart('limitCount');
        }

        $disableCacheParams = array();
        $disableCacheParams[] = $this->_getParamName();
        $c = $this->getData()->parent->getComponent();
        if ($c instanceof Kwc_Directories_List_View_Component && $c->hasSearchForm()) {
            $disableCacheParams[] = $c->getSearchForm()->componentId.'-post';
        }

        return array(
            'class' => get_class($this),
            'paramName' => $this->_getParamName(),
            'pages' => $this->_getPages(),
            'pagesize' => $pagesize,
            'disableCache' => false,
            'disableCacheParams' => $disableCacheParams
        );
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $pages = $partial->getParam('pages');
        return array(
            'pageLinks' => $this->_getPageLinks($pages, $nr)
        );
    }

    protected function _getPageLinks($pages, $currentPage)
    {
        $pageLinks = array();
        if ($currentPage >= 3 && !$this->_getSetting('nextPrevOnly')) {
            $pageLinks[] = $this->_getLinkData(1, 'first');
        }
        if ($currentPage >= 2) {
            $pageLinks[] = $this->_getLinkData($currentPage-1, 'previous');
        }

        $appendPagelinks = array();
        if (!$this->_getSetting('nextPrevOnly')) {
            $bigSteps = $this->_getSetting('bigPagingSteps');
            rsort($bigSteps);
            foreach ($bigSteps as $stepOffset) {
                if ($this->_getSetting('maxPagingLinks') < $stepOffset * 2) {
                    if ($currentPage >= $stepOffset + 1) {
                        $pageLinks[] = $this->_getLinkData($currentPage - $stepOffset);
                    }

                    if ($currentPage <= $pages - $stepOffset) {
                        array_unshift($appendPagelinks, $this->_getLinkData($currentPage + $stepOffset));
                    }
                }
            }
        }

        if ($currentPage < $pages) {
            $appendPagelinks[] = $this->_getLinkData($currentPage+1, 'next');
        }
        if ($currentPage < $pages - 1 && !$this->_getSetting('nextPrevOnly')) {
            $appendPagelinks[] = $this->_getLinkData($pages, 'last');
        }

        if (!$this->_getSetting('nextPrevOnly')) {
            $linksPerDirection = floor(
                ($this->_getSetting('maxPagingLinks') - (count($pageLinks) + count($appendPagelinks) + 1)) / 2
            );
            if ($linksPerDirection < 0) $linksPerDirection = 0;

            $fromPage = $currentPage - $linksPerDirection;
            $toPage = $currentPage + $linksPerDirection;
            if ($fromPage < 1) {
                $toPage += abs($fromPage - 1);
            }
            if ($toPage > $pages) {
                $fromPage -= ($toPage - $pages);
            }
            if ($fromPage < 1) $fromPage = 1;
            if ($toPage > $pages) $toPage = $pages;

            for ($i = $fromPage; $i <= $toPage; $i++) {
                $pageLinks[] = $this->_getLinkData($i);
            }
        }

        foreach ($appendPagelinks as $linkData) {
            $pageLinks[] = $linkData;
        }
        $ret = array();
        foreach ($pageLinks as $pageLink) {
            if ($pageLink) $ret[] = $pageLink;
        }
        return $ret;
    }

    public function getViewCacheSettings()
    {
        $ret = parent::getViewCacheSettings();

        if ($parentViewCacheSettings = $this->getData()->parent->getComponent()->getViewCacheSettings()) {
            $ret = array_merge($ret, $parentViewCacheSettings);
        }

        return $ret;
    }
}
