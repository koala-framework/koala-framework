<?php
class Vpc_Menu_Abstract extends Vpc_Abstract
{
    private $_currentPages;

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'level' => 'main' // (string)category oder (int)ebene
        ));
        $ret['componentName'] = trlVps('Menu');
        $ret['cssClass'] = 'webStandard';
        return $ret;

    }

    protected function _getMenuData($parentData = null)
    {
        $constraints = array('showInMenu' => true);
        $ret = array();
        $currentPages = array_reverse($this->_getCurrentPages());
        if ($parentData) {
            $ret = $parentData->getChildPages($constraints);
        } else {
            if (isset($this->getData()->level)) {
                $level = $this->getData()->level;
            } else {
                $level = $this->_getSetting('level');
            }
            if (is_string($level)) {
                $component = $this->getData()->parent;
                while ($component &&
                    !is_instance_of($component->componentClass, 'Vpc_Root_Category_Component')
                ) {
                    $component = $component->parent;
                }
                $category = null;
                if (!$component) {
                    //wenn seite nicht _unter_ einer kategorie anders suchen
                    $component = $this->getData()->parent;
                    while ($component && !$category) {
                        $component = $component->parent;
                        $category = $component->getChildComponent('-' . $level);
                        if ($category && !is_instance_of($category->componentClass, 'Vpc_Root_Category_Component')) {
                            $category = false;
                        }
                    }
                } else {
                    $category = $component->parent->getChildComponent('-' . $level);
                }
                if (!$category) throw new Vps_Exception("Category-Component '$level' not found");
                $ret = $category->getChildPages($constraints);
            } else {
                if (isset($currentPages[$level-2])) {
                    $ret = $currentPages[$level-2]->getChildPages($constraints);
                }
            }
        }
        $currentPageIds = array();
        foreach ($currentPages as $page) {
            if (!$page instanceof Vps_Component_Data_Root) {
                $currentPageIds[] = $page->getComponentId();
            }
        }
        $i = 0;
        foreach ($ret as $r) {
            $class = array();
            if ($i == 0) { $class[] = 'first'; }
            if ($i == count($ret)-1) { $class[] = 'last'; }
            if (in_array($r->componentId, $currentPageIds)) {
                $class[] ='current';
                $r->current = true;
            }
            $r->class = implode(' ', $class);
            $i++;
        }
        return $ret;
    }

    // Array mit IDs von aktueller Seiten und Parent Pages
    protected function _getCurrentPages()
    {
        if (!isset($this->_currentPages)) {
            $this->_currentPages = array();
            $p = $this->getData()->getPage();
            if (!$p) {
                throw new Vps_Exception('To show the menu currentPage has to be set for Vps_Component_Data_Root');
            }
            do {
                $this->_currentPages[] = $p;
            } while ($p = $p->getParentPage());
        }
        return $this->_currentPages;
    }
}
