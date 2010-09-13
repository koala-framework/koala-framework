<?php
class Vpc_Chained_Trl_Component extends Vpc_Chained_Abstract_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $copySettings = array('componentName', 'componentIcon', 'editComponents', 'viewCache');
        $copyFlags = array('processInput', 'menuCategory', 'chainedType', 'subroot');
        $ret = Vpc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Trl', $copySettings, $copyFlags);
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

        $ret['placeholder'] = Vpc_Abstract::getSetting($data->chained->componentClass, 'placeholder');
        foreach ($ret['placeholder'] as $k => $v) {
            $ret['placeholder'][$k] = $this->getData()->trlStaticExecute($v);
        }
        return $ret;
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
        $cls = substr($componentClass, strpos($componentClass, '.')+1);
        $cls = strpos($cls, '.') ? substr($cls, 0, strpos($cls, '.')) : $cls;
        return call_user_func(array($cls, 'getStaticCacheMeta'), $cls);
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();
        $ret[] = new Vps_Component_Cache_Meta_Component($this->getData()->chained);
        return $ret;
    }

    public static function getChainedByMaster($masterData, $chainedData, $select = array())
    {
        return Vpc_Chained_Abstract_Component::_getChainedByMaster($masterData, $chainedData, 'Trl', $select);
    }
}
