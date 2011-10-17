<?php
abstract class Kwc_Chained_Abstract_Component extends Kwc_Abstract
{
    public static function getChainedSettings($settings, $masterComponentClass, $prefix = 'Cc', $copySettings = array(), $copyFlags = array())
    {
        $ret = $settings;
        if (!$masterComponentClass) {
            throw new Kwf_Exception("This component requires a parameter");
        }
        $ret['masterComponentClass'] = $masterComponentClass;

        $ret['alternativeComponents'] = array();
        if (Kwc_Abstract::getFlag($masterComponentClass, 'hasAlternativeComponent')) {
            $alternativeComponents = call_user_func(array($masterComponentClass, 'getAlternativeComponents'), $masterComponentClass);
            foreach ($alternativeComponents as $acKey => $alternativeComponent) {
                $cmp = $alternativeComponent;
                $cmp = Kwc_Admin::getComponentClass($cmp, "{$prefix}_Component");
                if (!$cmp) $cmp = "Kwc_Chained_{$prefix}_Component";
                $cmp .= '.'.$alternativeComponent;
                $ret['alternativeComponents'][$acKey] = $cmp;
            }
        }

        $ret['generators'] = Kwc_Abstract::getSetting($masterComponentClass, 'generators');
        foreach ($ret['generators'] as $k=>&$g) {
            if (!is_array($g['component'])) $g['component'] = array($k=>$g['component']);
            foreach ($g['component'] as $key=>&$c) {
                if (!$c) continue;
                $masterC = $c;
                $c = Kwc_Admin::getComponentClass($c, "{$prefix}_Component");
                if (!$c) $c = "Kwc_Chained_{$prefix}_Component";
                $c .= '.'.$masterC;
                $g['masterComponentsMap'][$masterC] = $c;

                // Für jede Unterkomponente mit einer AlternativeComponent muss es auch einen Eintrag in der masterComponentsMap geben
                if (Kwc_Abstract::getFlag($masterC, 'hasAlternativeComponent')) {
                    $alternativeComponents = call_user_func(array($masterC, 'getAlternativeComponents'), $masterC);
                    foreach ($alternativeComponents as $alternativeComponent) {
                        $cmp = $alternativeComponent;
                        $cmp = Kwc_Admin::getComponentClass($cmp, "{$prefix}_Component");
                        if (!$cmp) $cmp = "Kwc_Chained_{$prefix}_Component";
                        $cmp .= '.'.$alternativeComponent;
                        $g['masterComponentsMap'][$alternativeComponent] = $cmp;
                    }
                }
            }
            if (!isset($g['class'])) {
                throw new Kwf_Exception("generator class is not set: '$k' in '$masterComponentClass'");
            }
            $g['chainedGenerator'] = $g['class'];
            $g['class'] = "Kwc_Chained_{$prefix}_Generator";
            if (isset($g['dbIdShortcut'])) unset($g['dbIdShortcut']);
            if (isset($g['plugins'])) {
                foreach ($g['plugins'] as $pKey => $plugin) {
                    $pc = Kwc_Admin::getComponentClass($plugin, "{$prefix}_Component");
                    if ($pc != $plugin) {
                        $g['plugins'][$pKey] = $pc;
                    } else {
                        unset($g['plugins'][$pKey]); // generator-plugins in Translation only if there is an translated plugin available
                    }
                }
            }
            if (isset($g['model'])) unset($g['model']);
        }
        foreach ($copySettings as $i) {
            if (Kwc_Abstract::hasSetting($masterComponentClass, $i)) {
                $ret[$i] = Kwc_Abstract::getSetting($masterComponentClass, $i);
            }
        }
        foreach ($copyFlags as $f) {
            $flags = Kwc_Abstract::getSetting($masterComponentClass, 'flags', false);
            if (isset($flags[$f])) {
                $ret['flags'][$f] = $flags[$f];
            }
        }
        return $ret;
    }

    public static function getAlternativeComponents($componentClass)
    {
        return Kwc_Abstract::getSetting($componentClass, 'alternativeComponents');
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
        if (!is_instance_of($data->chained->componentClass, 'Kwc_Chained_Abstract_Component')) {
            $ret['linkTemplate'] = self::getTemplateFile($data->chained->componentClass);
        }

        $ret['componentClass'] = get_class($this);

        $ret['placeholder'] = Kwc_Abstract::getSetting($data->chained->componentClass, 'placeholder');
        foreach ($ret['placeholder'] as $k => $v) {
            $ret['placeholder'][$k] = $this->getData()->trlStaticExecute($v);
        }
        return $ret;
    }

    public static function getPartialClass()
    {
        return $this->getData()->chained->getComponent()->getPartialClass();
    }

    public function getPartialParams()
    {
        return $this->getData()->chained->getComponent()->getPartialParams();
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret = $this->getData()->chained->getComponent()->getPartialVars($partial, $nr, $info);
        $ret['linkTemplate'] = self::getTemplateFile($this->getData()->chained->componentClass, 'Partial');
        return $ret;
    }

    public function getPartialCacheVars($nr)
    {
        return $this->getData()->chained->getComponent()->getPartialCacheVars($nr);
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $sourceComponentClass = substr($componentClass, strpos($componentClass, '.')+1);
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Kwf_Component_Cache_Meta_Static_Chained($sourceComponentClass);
        return $ret;
    }

    public static function getChainedByMaster($masterData, $chainedData, $chainedType, $select = array())
    {
        return self::_getChainedByMaster($masterData, $chainedData, $chainedType, $select);
    }

    protected static final function _getChainedByMaster($masterData, $chainedData, $chainedType, $select = array())
    {
        if (!$masterData) return null;

        while ($chainedData) {
            if (Kwc_Abstract::getFlag($chainedData->componentClass, 'chainedType') == $chainedType) {
                break;
            }
            $chainedData = $chainedData->parent;
        }
        $c = $masterData;
        $ids = array();
        $subrootReached = false;
        while ($c) {
            $pos = max(
                strrpos($c->componentId, '-'),
                strrpos($c->componentId, '_')
            );
            $id = substr($c->componentId, $pos);
            if (Kwc_Abstract::getFlag($c->componentClass, 'chainedType') == $chainedType) {
                $subrootReached = true;
                break;
            }
            $skipParents = false;
            if ((int)$id > 0) { // nicht mit is_numeric wegen Bindestrich, das als minus interpretiert wird
                $id = '_' . $id;
                $skipParents = true;
            }
            $c = $c->parent;
            if ($c) {
                $ids[] = $id;
                //bei pages die parent ids auslassen
                if ($skipParents) {
                    while (is_numeric($c->componentId)) {
                        $c = $c->parent;
                    }
                }
            }
        }
        if (!$subrootReached) return $masterData;
        $ret = $chainedData;
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }
        foreach (array_reverse($ids) as $id) {
            $select->whereId($id);
            $ret = $ret->getChildComponent($select);
            if (!$ret) return null;
        }
        return $ret;
    }
}