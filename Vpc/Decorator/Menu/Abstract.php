<?p
class Vpc_Decorator_Menu_Abstract extends Vpc_Decorator_Abstra

    protected function _getMenuData($pages, $currentPageId
   
        $return = array(
        foreach ($pages as $i => $page)
            $data = $this->getPageCollection()->getPageData($page
            if ($data['hide']) { continue;
            $class = '
            if ($i == 0) $class .= ' first
            if ($i == sizeof($pages)-1) $class .= ' last
            $isCurrent = in_array($page->getPageId(), $currentPageIds
            if ($isCurrent) $class .= ' current
            $return[] = arra
                'href'    => $data['url'
                'text'    => $data['name'
                'current' => $isCurren
                'class'   => trim($class
                'rel'     => $data['rel
            
       
        return $retur
   

