<?php
class Kwc_Menu_BreadCrumbs_Component extends Kwc_Menu_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['separator'] = '»';
        $ret['showHome'] = false;
        $ret['showCurrentPage'] = true;
        return $ret;
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        return false;
    }

    public static function getAlternativeComponents($componentClass)
    {
        return array();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['separator'] = $this->_getSetting('separator');
        $ret['links'] = array();
        $page = $this->getData()->getPage();
        do {
            $ret['links'][] = $page;
        } while ($page = $page->getParentPage());
        $page = $this->getData()->getPage();
        if ($this->_getSetting('showHome') && $page) {
            if (!isset($page->isHome) || !$page->isHome) {
                $home = Kwf_Component_Data_Root::getInstance()->getRecursiveChildComponents(array(
                    'home' => true,
                    'subRoot' => $this->getData()
                ), array());
                if ($home) {
                    $ret['links'][] = $home[0];
                }
            }
        }
        $ret['links'] = array_reverse($ret['links']);
        if (count($ret['links']) && !$this->_getSetting('showCurrentPage')) {
            array_pop($ret['links']);
        }

        $ret['items'] = array();
        $i = 0;
        foreach ($ret['links'] as $l) {
            $class = '';
            if ($i == 0) $class .= 'first ';
            if ($i == count($ret['links'])-1) {
                $class .= 'last ';
            }
            $ret['items'][] = array(
                'data' => $l,
                'class' => trim($class),
                'last' => $i == count($ret['links'])-1
            );
            $i++;
        }
        return $ret;
    }
}
