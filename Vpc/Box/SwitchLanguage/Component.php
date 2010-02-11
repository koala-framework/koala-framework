<?php
class Vpc_Box_SwitchLanguage_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $languages = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Root_LanguageRoot_Language_Component');
        $ret['languages'] = array();
        foreach ($languages as $l) {
            $home = $l->getChildPage(array('home'=>true));
            if ($home) {
                $ret['languages'][] = array(
                    'language' => $l->id,
                    'home' => $home,
                );
            }
        }
        return $ret;
    }
}
