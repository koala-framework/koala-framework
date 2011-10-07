<?php
class Vpc_Box_SwitchLanguage_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['separator'] = ' / ';
        $ret['showCurrent'] = true;
        return $ret;
    }

    protected function _getLanguages()
    {
        $languages = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_LanguageRoot_Language_Component');
        $languages = array_merge($languages, Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_TrlRoot_Master_Component'));
        $languages = array_merge($languages, Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_TrlRoot_Chained_Component'));
        return $languages;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['separator'] = $this->_getSetting('separator');
        $languages = $this->_getLanguages();
        $ret['languages'] = array();
        foreach ($languages as $l) {
            if (!$this->_getSetting('showCurrent')) {
                if ($this->getData()->getLanguageData()->componentId == $l->componentId) {
                    continue;
                }
            }
            $masterPage = $this->getData()->getPage();
            if (isset($masterPage->chained)) {
                $masterPage = $masterPage->chained; //TODO: nicht sauber
            }
            $page = null;
            if ($masterPage) {
                if (is_instance_of($l->componentClass, 'Vpc_Root_TrlRoot_Chained_Component')) {
                    $page = Vpc_Chained_Trl_Component::getChainedByMaster($masterPage, $l);
                } else if (is_instance_of($l->componentClass, 'Vpc_Root_TrlRoot_Master_Component')) {
                    $page = $masterPage;
                }
                $p = $page;
                while ($p && $page) {
                    //TODO dafür müsste es eine bessere methode geben
                    if (isset($p->row) && isset($p->row->visible) && !$p->row->visible) {
                        $page = null;
                    }
                    $p = $p->parent;
                }
            }
            $home = $l->getChildPage(array('home'=>true));
            if ($home) {
                $ret['languages'][] = array(
                    'language' => $l->id,
                    'home' => $home,
                    'page' => $page ? $page : $home,
                    'flag' => $l->getChildComponent('-flag'),
                    'name' => $l->name,
                    'current' => $this->getData()->getLanguageData()->componentId == $l->componentId
                );
            }
        }
        if ($this->_getSetting('showCurrent') && count($ret['languages']) == 1) {
            $ret['languages'] = array();
        }
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = Vpc_Menu_Abstract_Component::getStaticCacheMeta($componentClass);
        foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
            foreach (Vpc_Abstract::getSetting($componentClass, 'generators') as $key => $generator) {
                if (is_instance_of($generator['class'], 'Vpc_Chained_Abstract_ChainedGenerator')) {
                    $generator = current(Vps_Component_Generator_Abstract::getInstances(
                        $componentClass, array('generator' => $key))
                    );
                    $ret[] = new Vps_Component_Cache_Meta_Static_Model($generator->getModel());
                }
            }
        }
        return $ret;
    }
}
