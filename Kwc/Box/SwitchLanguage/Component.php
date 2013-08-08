<?php
class Kwc_Box_SwitchLanguage_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['separator'] = ' / ';
        $ret['showCurrent'] = true;
        $ret['plugins'] = array('Kwc_Box_SwitchLanguage_Plugin');
        return $ret;
    }

    protected function _getLanguages()
    {
        $languages = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Root_LanguageRoot_Language_Component', array('subroot'=>$this->getData()));
        $languages = array_merge($languages, Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Root_TrlRoot_Master_Component', array('subroot'=>$this->getData())));
        $languages = array_merge($languages, Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Root_TrlRoot_Chained_Component', array('subroot'=>$this->getData())));
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
                if ($this->getData()->getLanguage() == $l->getLanguage()) {
                    continue;
                }
            }
            $masterPage = $this->getData()->getPage();
            if (isset($masterPage->chained)) {
                $masterPage = $masterPage->chained; //TODO: nicht sauber
            }
            $page = null;
            if ($masterPage) {
                if (is_instance_of($l->componentClass, 'Kwc_Root_TrlRoot_Chained_Component')) {
                    $page = Kwc_Chained_Trl_Component::getChainedByMaster($masterPage, $l);
                } else if (is_instance_of($l->componentClass, 'Kwc_Root_TrlRoot_Master_Component')) {
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
                    'current' => $this->getData()->getLanguage() == $l->getLanguage()
                );
            }
        }
        if ($this->_getSetting('showCurrent') && count($ret['languages']) == 1) {
            $ret['languages'] = array();
        }
        return $ret;
    }

    public function hasContent()
    {
        return !(count($this->_getLanguages()) <= 1);
    }
}
