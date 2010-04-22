<?php
class Vpc_Chained_Trl_MasterAsChild_Component extends Vpc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => $masterComponentClass,
        );
        try {
            $ret['componentName'] = Vpc_Abstract::getSetting($masterComponentClass, 'componentName');
        } catch (Exception $e) {}
        try {
            $ret['componentIcon'] = Vpc_Abstract::getSetting($masterComponentClass, 'componentIcon');
        } catch (Exception $e) {}
        return $ret;
    }

    public function sendContent()
    {
        $this->getData()->getChildComponent('-child')->getComponent()->sendContent();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['child'] = $this->getData()->getChildComponent('-child');
        return $ret;
    }

}
