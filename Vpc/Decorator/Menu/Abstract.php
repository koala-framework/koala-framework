<?php
class Vpc_Decorator_Menu_Abstract extends Vpc_Decorator_Abstract
{
    protected function _getMenuData($pages, $currentPageIds)
    {
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
                'href'    => $data['url'],
                'text'    => $data['name'],
                'current' => $isCurrent,
                'class'   => trim($class),
                'rel'     => $data['rel']
            );
        }
        return $return;
    }
}
