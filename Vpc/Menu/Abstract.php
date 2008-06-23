<?php
class Vpc_Menu_Abstract extends Vpc_Abstract
{
    private $_currentPages;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'level' => 'main' // (string)pagetype oder (int)ebene
        ));
    }

    protected function _getMenuData($parentData = null)
    {
        $constraints = array('showInMenu' => true);
        $ret = array();
        $currentPages = array_reverse($this->_getCurrentPages());
        if ($parentData) {
            $ret = $parentData->getChildPages($constraints);
        } else {
            $level = $this->_getSetting('level');
            if (is_string($level)) {
                $constraints['type'] = $level;
                $ret = Vps_Component_Data_Root::getInstance()->getChildPages($constraints);
            } else {
                if (isset($currentPages[$level-2])) {
                    $ret = $currentPages[$level-2]->getChildPages($constraints);
                }
            }
        }
        $first = reset($ret);
        $last = end($ret);
        $currentPageIds = array();
        foreach ($currentPages as $page) {
            if (!$page instanceof Vps_Component_Data_Root) {
                $currentPageIds[] = $page->getComponentId();
            }
        }
        foreach ($ret as $i=>$r) {
            $class = array();
            if ($i == 0) { $class[] = 'first'; }
            if ($i == count($ret)-1) { $class[] = 'last'; }
            if (in_array($r->getComponentId(), $currentPageIds)) {
                $class[] ='current';
            }
            $r->class = implode(' ', $class);
        }
        return $ret;
    }
    
    // Array mit IDs von aktueller Seiten und Parent Pages
    protected function _getCurrentPages()
    {
        if (!isset($this->_currentPages)) {
            $this->_currentPages = array();
            $p = $this->getData()->getPage();
            do {
                $this->_currentPages[] = $p;
            } while ($p = $p->getParentPage());
        }
        return $this->_currentPages;
    }
}
