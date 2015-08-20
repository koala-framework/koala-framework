<?php
class Kwc_Chained_Abstract_MasterAsChild_Component extends Kwc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        if (!$masterComponentClass) {
            throw new Kwf_Exception("This component requires a parameter");
        }
        $ret['masterComponentClass'] = $masterComponentClass;
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => $masterComponentClass,
        );
        $ret['editComponents'] = array('child');
        try {
            $ret['componentName'] = Kwc_Abstract::getSetting($masterComponentClass, 'componentName');
        } catch (Exception $e) {}
        try {
            $ret['componentIcon'] = Kwc_Abstract::getSetting($masterComponentClass, 'componentIcon');
        } catch (Exception $e) {}

        if (Kwc_Abstract::getFlag($masterComponentClass, 'hasInjectIntoRenderedHtml')) {
            $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        }
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

    public function injectIntoRenderedHtml($html)
    {
        return $this->getData()->getChildComponent('-child')->getComponent()->injectIntoRenderedHtml($html);
    }
}
