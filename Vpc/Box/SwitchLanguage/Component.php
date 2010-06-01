<?php
class Vpc_Box_SwitchLanguage_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['separator'] = ' / ';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['separator'] = $this->_getSetting('separator');
        $languages = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_LanguageRoot_Language_Component');
        $languages = array_merge($languages, Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_TrlRoot_Master_Component'));
        $languages = array_merge($languages, Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_TrlRoot_Chained_Component'));
        $ret['languages'] = array();
        foreach ($languages as $l) {
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
            }
            $home = $l->getChildPage(array('home'=>true));
            if ($home) {
                $ret['languages'][] = array(
                    'language' => $l->id,
                    'home' => $home,
                    'page' => $page ? $page : $home,
                    'flag' => $l->getChildComponent('-flag'),
                    'name' => $l->name
                );
            }
        }
        return $ret;
    }
}
