<?php
class Vpc_Root_TrlRoot_Chained_Component extends Vpc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();

        if (!$masterComponentClass) {
            throw new Vps_Exception("This component requires a parameter");
        }
        $ret['masterComponentClass'] = $masterComponentClass;
        $ret['generators'] = Vpc_Abstract::getSetting($masterComponentClass, 'generators', false);
        foreach ($ret['generators'] as $k=>&$g) {
            if (!is_array($g['component'])) $g['component'] = array($k=>$g['component']);
            foreach ($g['component'] as $key=>&$c) {
                if (!$c) continue;
                $masterC = $c;
                $c = Vpc_Admin::getComponentClass($c, 'Trl_Component');
                if (!$c) $c = 'Vpc_Chained_Trl_Component';
                $c .= '.'.$masterC;
                $g['masterComponentsMap'][$masterC] = $key;
            }
            $g['chainedGenerator'] = $g['class'];
            $g['class'] = 'Vpc_Chained_Trl_Generator';
        }
        $ret['flags']['showInPageTreeAdmin'] = true;
        $ret['flags']['hasHome'] = true;
        $ret['flags']['hasLanguage'] = true;
        $ret['flags']['subroot'] = 'language';
        if (Vpc_Abstract::hasSetting($masterComponentClass, 'editComponents', false)) {
            $ret['editComponents'] = Vpc_Abstract::getSetting($masterComponentClass, 'editComponents', false);
        }
        return $ret;
    }

    public function getLanguage()
    {
        return $this->getData()->language;
    }
}
