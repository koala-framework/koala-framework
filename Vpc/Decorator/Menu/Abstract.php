<?php
class Vpc_Decorator_Menu_Abstract extends Vpc_Decorator_Abstract
{
    private $_currentPageIds;

    protected function _getMenuData($pages)
    {
        $currentPageIds = $this->_getCurrentPageIds();

        $return = array();
        foreach ($pages as $i => $page) {
            $data = $this->getPageCollection()->getPageData($page);
            if ($data['hide']) continue;
            $class = '';
            if ($i == 0) $class .= ' first';
            if ($i == sizeof($pages)-1) $class .= ' last';
            $isCurrent = in_array($page->getPageId(), $currentPageIds);
            if ($isCurrent) $class .= ' current';
            $return[] = array(
                'page'    => $page,
                'href'    => $data['url'],
                'text'    => $data['name'],
                'current' => $isCurrent,
                'class'   => trim($class),
                'rel'     => $data['rel']
            );
        }
        return $return;
    }

    // Array mit IDs von aktueller Seiten und Parent Pages
    protected function _getCurrentPageIds()
    {
        if (!isset($this->_currentPageIds)) {
            $pc = $this->getPageCollection();
            $this->_currentPageIds = array();
            $p = $pc->getCurrentPage();
            do {
                $this->_currentPageIds[] = $p->getPageId();
            } while ($p = $pc->getParentPage($p));
        }
        return $this->_currentPageIds;
    }
}
