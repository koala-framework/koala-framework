<?php
class Vpc_Box_InheritContent_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => false
        );
        $ret['editComponents'] = array('child');

        //TODO: viewcache nicht deaktiveren
        //cache löschen muss dazu korrekt eingebaut werden
        //$ret['viewCache'] = false;

        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_None';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['child'] = $this->getContentChild();
        return $ret;
    }

    public function getExportData()
    {
        return $this->getContentChild()->getComponent()->getExportData();
    }

    public function getContentChild()
    {
        $page = $this->getData();
        $ids = array();
        while ($page && !$page->inherits) {
            $ids[] = $page->id;
            $page = $page->parent;
            if ($page instanceof Vps_Component_Data_Root) break;
        }
        $ids = array_reverse($ids);
        $page = $this->getData();
        do {
            while ($page && !$page->inherits) {
                $page = $page->parent;
                if ($page instanceof Vps_Component_Data_Root) break;
            }
            $ic = $page;
            foreach ($ids as $id) {
                $ic = $ic->getChildComponent('-'.$id);
                if (!$ic) {
                    return null;
                }
            }
            $c = $ic->getChildComponent(array('generator' => 'child'));
            if (!$c) break; //box wurde überschrieben
            if ($page instanceof Vps_Component_Data_Root) break;
            $page = $page->parent;
        } while(!$c->hasContent());
        return $c;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $generators = Vpc_Abstract::getSetting($componentClass, 'generators');
        if (isset($generators['child']) && $generators['child']['component']) {
            $childClass = $generators['child']['component'];
            // TODO: es sollte nur mit Pattern wie "%-ic-child" gemacht werden, geht aber noch nicht
            if ($childClass) $ret[] = new Vpc_Box_InheritContent_CacheMeta($childClass);
        }
        return $ret;
    }

    public function hasContent()
    {
        return true;
    }
}
