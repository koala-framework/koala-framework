<?php
class Vpc_Advanced_Sitemap_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Sitemap');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['levels'] = $this->getRow()->levels;
        $ret['target'] = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getRow()->target, array('limit'=>1));
        $ret['listHtml'] = '';
        if ($ret['target']) {
            $ret['listHtml'] = $this->_getListHtml($ret['target'], 0);
        }
        return $ret;
    }

    //not in template for easier recursion
    private function _getListHtml(Vps_Component_Data $c, $level)
    {
        $ret = '';
        $level++;
        $select = new Vps_Component_Select();
        $select->whereShowInMenu(true);
        $ret .= "<ul>\n";
        foreach ($c->getChildPages($select) as $child) {
            $ret .= "<li>\n";
            $helper = new Vps_Component_View_Helper_ComponentLink();
            $ret .= $helper->componentLink($child);
            $ret .= "\n";
            if ($level < $this->getRow()->levels) {
                $ret .= $this->_getListHtml($child, $level);
            }
            $ret .= "</li>\n";
        }
        $ret .= "</ul>\n";
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                if (!isset($generator['showInMenu']) || !$generator['showInMenu']) continue;
                $generator = current(Vps_Component_Generator_Abstract::getInstances(
                    $class, array('generator' => $key))
                );
                if (!$generator->getGeneratorFlag('page') || !$generator->getGeneratorFlag('table')) continue;
                $ret[] = new Vps_Component_Cache_Meta_Static_Model($generator->getModel());
            }
        }

        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vps_Component_Model');

        return $ret;
    }
}