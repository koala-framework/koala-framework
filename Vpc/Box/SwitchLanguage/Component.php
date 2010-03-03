<?php
class Vpc_Box_SwitchLanguage_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $languages = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_LanguageRoot_Language_Component');
        $languages = array_merge($languages, Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_TrlRoot_Master_Component'));
        $languages = array_merge($languages, Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Chained_Trl_Base_Component'));
        $ret['languages'] = array();
        foreach ($languages as $l) {
            $home = $l->getChildPage(array('home'=>true));
            if ($home) {
                $ret['languages'][] = array(
                    'language' => $l->id,
                    'home' => $home,
                    'flag' => $l->getChildComponent('-flag')
                );
            }
        }
        return $ret;
    }
}
