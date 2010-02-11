<?php
class Vpc_Chained_Trl_Component extends Vpc_Abstract
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
            foreach ($g['component'] as &$c) {
                $masterC = $c;
                $c = Vpc_Admin::getComponentClass($c, 'Trl_Component');
                if (!$c) $c = 'Vpc_Chained_Trl_Component';
                $c .= '.'.$masterC;
                $g['masterComponentsMap'][$masterC] = $c;
            }
            $g['chainedGenerator'] = $g['class'];
            $g['class'] = 'Vpc_Chained_Trl_Generator';
        }
        try {
            $ret['componentName'] = Vpc_Abstract::getSetting($masterComponentClass, 'componentName', false);
        } catch (Exception $e) {}
        try {
            $ret['componentIcon'] = Vpc_Abstract::getSetting($masterComponentClass, 'componentIcon', false);
        } catch (Exception $e) {}

        foreach (array('showInPageTreeAdmin', 'processInput') as $f) {
            $flags = Vpc_Abstract::getSetting($masterComponentClass, 'flags', false);
            if (isset($flags[$f])) {
                $ret['flags'][$f] = $flags[$f];
            }
        }
        return $ret;
    }

    public function preProcessInput($postData)
    {
        $c = $this->getData()->chained->getComponent();
        if (method_exists($c, 'preProcessInput')) {
            $c->preProcessInput($postData);
        }
    }

    public function processInput($postData)
    {
        $c = $this->getData()->chained->getComponent();
        if (method_exists($c, 'processInput')) {
            $c->processInput($postData);
        }
    }

    public function getTemplateVars()
    {
        $data = $this->getData();
        $ret = $data->chained->getComponent()->getTemplateVars();
        $ret['chained'] = $data->chained;
        $ret['linkTemplate'] = self::getTemplateFile($data->chained->componentClass);

        $ret['componentClass'] = get_class($this);
        return $ret;
    }
}
