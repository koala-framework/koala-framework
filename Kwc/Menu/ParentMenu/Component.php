<?php
class Kwc_Menu_ParentMenu_Component extends Kwc_Abstract
{
    public static function getSettings($menuComponentClass)
    {
        $ret = parent::getSettings();
        $generators = Kwc_Abstract::getSetting($menuComponentClass, 'generators');
        if (isset($generators['subMenu'])) {
            $ret['generators']['subMenu'] = $generators['subMenu'];
        }
        $ret['plugins'] = Kwc_Abstract::getSetting($menuComponentClass, 'plugins');
        $ret['viewCache'] = Kwc_Abstract::getSetting($menuComponentClass, 'viewCache');
        $ret['menuComponentClass'] = $menuComponentClass;
        return $ret;
    }

    public function getActiveViewPlugins()
    {
        return $this->_getParentContentData()->getComponent()->getActiveViewPlugins();
    }

    private function _getParentContentData()
    {
        $data = $this->getData();
        $ids = array();
        while ($data && !$data->inherits) {
            $ids[] = strrchr($data->componentId, '-');
            $data = $data->parent;
        }
        while ($data) {
            if ($data->inherits) {
                $d = $data;
                foreach (array_reverse($ids) as $id) {
                    $d = $d->getChildComponent($id);
                }
                if (!$d) break;
                if ($d->componentClass != $this->getData()->componentClass) {
                    return $d;
                }
            }
            $data = $data->parent;
        }
        return null;
    }

    // Array mit aktueller Seiten und Parent Pages
    protected function _getCurrentPages()
    {
        $ret = array();
        $p = $this->getData()->getPage();
        while ($p) {
            $ret[] = $p;
            $p = $p->getParentPage();
        }
        return $ret;
    }

    //helper that sets current to current page
    private function _processMenuSetCurrent(&$ret)
    {
        $currentPages = array_reverse($this->_getCurrentPages());

        $currentPageIds = array();
        foreach ($currentPages as $page) {
            if (!$page instanceof Kwf_Component_Data_Root) {
                $currentPageIds[] = $page->componentId;
                $selected = $page->componentId; //the last one is selected
            }
        }
        foreach ($ret as $k=>$i) {
            if (in_array($i['data']->componentId, $currentPageIds)) {
                $ret[$k]['current'] = true;
                $ret[$k]['class'] .= ' current';
                if ($selected == $i['data']->componentId) {
                    $ret[$k]['selected'] = true;
                    $ret[$k]['class'] .= ' selected';
                }
            }
        }
    }

    public function getMenuData($parentData = null, $select = array())
    {
        $ret = $this->_getParentContentData()->getComponent()->getMenuData($parentData, $select);
        $this->_processMenuSetCurrent($ret);
        return $ret;
    }

    public function getTemplateVars()
    {
        $menu = $this->_getParentContentData();
        if (!is_instance_of($menu->componentClass, 'Kwc_Menu_Abstract_Component')) {
            throw new Kwf_Exception("got invalid menu component '$menu->componentClass'");
        }

        $ret = $menu->getComponent()->getTemplateVars();

        $ret['includeTemplate'] = self::getTemplateFile($menu->componentClass);

        $ret['subMenu'] = $this->getData()->getChildComponent('-subMenu');

        $this->_processMenuSetCurrent($ret['menu']);

        return $ret;
    }

    public function hasContent()
    {
        return $this->_getParentContentData()->getComponent()->hasContent();
    }
}
