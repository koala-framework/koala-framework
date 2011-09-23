<?php
class Vpc_Menu_ParentMenu_Component extends Vpc_Abstract
{
    public static function getSettings($menuComponentClass)
    {
        $ret = parent::getSettings();
        $generators = Vpc_Abstract::getSetting($menuComponentClass, 'generators');
        if (isset($generators['subMenu'])) {
            $ret['generators']['subMenu'] = $generators['subMenu'];
        }
        return $ret;
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

    public function getMenuData()
    {
        return $this->_getParentContentData()->getComponent()->getMenuData();
    }

    public function getTemplateVars()
    {
        $menu = $this->_getParentContentData();
        if (!is_instance_of($menu->componentClass, 'Vpc_Menu_Abstract_Component')) {
            throw new Vps_Exception("got invalid menu component '$menu->componentClass'");
        }

        $ret = $menu->getComponent()->getTemplateVars();

        $ret['includeTemplate'] = self::getTemplateFile($menu->componentClass);

        $ret['subMenu'] = $this->getData()->getChildComponent('-subMenu');

        $currentPages = array_reverse($this->_getCurrentPages());

        $currentPageIds = array();
        foreach ($currentPages as $page) {
            if (!$page instanceof Vps_Component_Data_Root) {
                $currentPageIds[] = $page->getComponentId();
            }
        }
        foreach ($ret['menu'] as $k=>$i) {
            if (in_array($i['data']->componentId, $currentPageIds)) {
                $ret['menu'][$k]['current'] = true;
                $ret['menu'][$k]['class'] .= ' current';
            }
        }

        return $ret;
    }
}
