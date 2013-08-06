<?php
abstract class Kwc_Chained_Abstract_Component extends Kwc_Abstract
{
    private $_pdfWriter;

    public static function getChainedComponentClass($masterComponentClass, $prefix)
    {
        $cmp = $masterComponentClass;
        $cmp = Kwc_Admin::getComponentClass($cmp, "{$prefix}_Component");
        if (!$cmp) $cmp = "Kwc_Chained_{$prefix}_Component";
        $cmp .= '.'.$masterComponentClass;
        return $cmp;
    }

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
                $ret['alternativeComponents'][$acKey] = self::getChainedComponentClass($alternativeComponent, $prefix);
            }
        }
        $ret['generators'] = Kwc_Abstract::getSetting($masterComponentClass, 'generators');
        foreach ($ret['generators'] as $k=>$g) {
            $ret['generators'][$k] = self::createChainedGenerator($masterComponentClass, $k, $prefix);
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

    public static function createChainedGenerator($class, $key, $prefix)
    {
        $generators = Kwc_Abstract::getSetting($class, 'generators');
        $g = $generators[$key];
        if (!isset($g['class'])) throw new Kwf_Exception("generator class is not set for component '$class' generator '$key'");
        if (!is_array($g['component'])) $g['component'] = array($key => $g['component']);
        foreach ($g['component'] as &$c) {
            if (!$c) continue;
            $masterC = $c;

            if (is_instance_of($c, 'Kwc_Chained_CopyPages_Component') || is_instance_of($c, 'Kwc_Chained_CopyPages_Cc_Component')) {
                continue;
            }

            $c = self::getChainedComponentClass($c, $prefix);
            $g['masterComponentsMap'][$masterC] = $c;

            // Für jede Unterkomponente mit einer AlternativeComponent muss es auch einen Eintrag in der masterComponentsMap geben
            if (Kwc_Abstract::getFlag($masterC, 'hasAlternativeComponent')) {
                $alternativeComponents = call_user_func(array($masterC, 'getAlternativeComponents'), $masterC);
                foreach ($alternativeComponents as $alternativeComponent) {
                    $g['masterComponentsMap'][$alternativeComponent] = self::getChainedComponentClass($alternativeComponent, $prefix);
                }
            }
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
        return $g;
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

    public static function getPartialClass($componentClass)
    {
        $mc = Kwc_Abstract::getSetting($componentClass, 'masterComponentClass');
        return call_user_func(array($mc, 'getPartialClass'), $mc);
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

    public function getContentWidth()
    {
        return $this->getData()->chained->getComponent()->getContentWidth();
    }

    public function getPdfWriter($pdf)
    {
        if (!isset($this->_pdfWriter)) {
            $class = Kwc_Admin::getComponentFile($this->getData()->chained->componentClass, 'Pdf', 'php', true);
            $this->_pdfWriter = new $class($this, $pdf);
        }
        return $this->_pdfWriter;
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
        if (!$subrootReached) return null;
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

    public static function getAllChainedByMaster($master, $chainedType, $parentDataSelect = array())
    {
        $cacheId = 'hasChainedBMCls-'.Kwf_Component_Data_Root::getComponentClass();
        $classes = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if ($classes === false) {
            $classes = array();
            foreach (Kwc_Abstract::getComponentClasses() as $cls) {
                if (Kwc_Abstract::getFlag($cls, 'hasAllChainedByMaster')) {
                    $classes[] = $cls;
                }
            }
            Kwf_Cache_SimpleStatic::add($cacheId, $classes);
        }
        $ret = array();
        foreach ($classes as $cls) {
            $c = strpos($cls, '.') ? substr($cls, 0, strpos($cls, '.')) : $cls;
            $ret = array_merge($ret, call_user_func(array($c, 'getAllChainedByMasterFromChainedStart'), $cls, $master, $chainedType, $parentDataSelect));
        }
        return $ret;
    }
}
