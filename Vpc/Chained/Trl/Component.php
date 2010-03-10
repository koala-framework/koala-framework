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
        $ret['generators'] = Vpc_Abstract::getSetting($masterComponentClass, 'generators');
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
            if (isset($g['dbIdShortcut'])) unset($g['dbIdShortcut']);
        }
        try {
            $ret['componentName'] = Vpc_Abstract::getSetting($masterComponentClass, 'componentName');
        } catch (Exception $e) {}
        try {
            $ret['componentIcon'] = Vpc_Abstract::getSetting($masterComponentClass, 'componentIcon');
        } catch (Exception $e) {}

        foreach (array('showInPageTreeAdmin', 'processInput', 'menuCategory') as $f) {
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
        $ret['data'] = $data;
        $ret['chained'] = $data->chained;
        $ret['linkTemplate'] = self::getTemplateFile($data->chained->componentClass);

        $ret['componentClass'] = get_class($this);
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $ret = array_merge($ret, $this->getData()->chained->getComponent()->getCacheVars());
        return $ret;
    }

    public function getPartialClass()
    {
        return $this->getData()->chained->getComponent()->getPartialClass();
    }

    public function getPartialParams()
    {
        return $this->getData()->chained->getComponent()->getPartialParams();
    }

    public static function getStaticCacheVars($componentClass)
    {
        $cls = substr($componentClass, strpos($componentClass, '.')+1);
        return call_user_func(array($cls, 'getStaticCacheVars'), $cls);
    }

    public static function getChainedByMaster($masterData, $chainedData)
    {
        if (!$masterData) return null;

        while ($chainedData) {
            if (is_instance_of($chainedData->componentClass, 'Vpc_Root_TrlRoot_Chained_Component')) { //wen nötig stattdessen ein neues flag erstellen
                break;
            }
            $chainedData = $chainedData->parent;
        }

        $c = $masterData;
        $ids = array();
        while ($c) {
            $pos = max(
                strrpos($c->componentId, '-'),
                strrpos($c->componentId, '_')
            );
            $id = substr($c->componentId, $pos);
            if (is_instance_of($c->componentClass, 'Vpc_Root_TrlRoot_Master_Component')) { //wen nötig stattdessen ein neues erstellen
                break;
            }
            if ((int)$id > 0) $id = '_' . $id;
            $c = $c->parent;
            if ($c) $ids[] = $id;
        }
        $ret = $chainedData;
        foreach (array_reverse($ids) as $id) {
            $ret = $ret->getChildComponent($id);
        }
        return $ret;
    }
}
