<?php
class Vpc_Chained_Abstract_MasterAsChild_Component extends Vpc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        if (!$masterComponentClass) {
            throw new Vps_Exception("This component requires a parameter");
        }
        $ret['masterComponentClass'] = $masterComponentClass;
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => $masterComponentClass,
        );
        $ret['editComponents'] = array('child');
        try {
            $ret['componentName'] = Vpc_Abstract::getSetting($masterComponentClass, 'componentName');
        } catch (Exception $e) {}
        try {
            $ret['componentIcon'] = Vpc_Abstract::getSetting($masterComponentClass, 'componentIcon');
        } catch (Exception $e) {}
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['child'] = $this->getData()->getChildComponent('-child');
        return $ret;
    }

    public function hasContent()
    {
        return $this->getData()->getChildComponent('-child')->hasContent();
    }
}
