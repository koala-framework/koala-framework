<?php
abstract class Kwf_Component_Layout_Abstract
{
    private static $_supportedContexts;
    private static $_supportedChildContexts;

    protected $_class;
    public function __construct($class) //for the moment we need class only
    {
        $this->_class = $class;
    }

    protected function _getSetting($name)
    {
        return Kwc_Abstract::getSetting($this->_class, $name);
    }

    protected function _hasSetting($name)
    {
        return Kwc_Abstract::hasSetting($this->_class, $name);
    }

    /**
     * @return self
     */
    public static function getInstance($class)
    {
        static $i = array();
        if (!isset($i[$class])) {
            if (!Kwc_Abstract::hasSetting($class, 'layoutClass')) {
                throw new Kwf_Exception("No layoutClass set for '$class'");
            }
            $layout = Kwc_Abstract::getSetting($class, 'layoutClass');
            $i[$class] = new $layout($class);
        }
        return $i[$class];
    }

    /**
     * @internal
     */
    public static function _buildAll($componentClasses)
    {
        foreach ($componentClasses as $cmp) {
            if (Kwc_Abstract::hasSetting($cmp, 'layoutClass')) {
                self::$_supportedContexts[$cmp] = self::getInstance($cmp)->calcSupportedContexts();
                self::$_supportedChildContexts[$cmp] = self::getInstance($cmp)->calcSupportedChildContexts();
            }
        }
        $data = array(
            'contexts' => self::$_supportedContexts,
            'childContexts' => self::$_supportedChildContexts,
        );

        file_put_contents('build/component/layoutcontexts', serialize($data));
    }

    private static function _loadFromBuild()
    {
        if (!isset(self::$_supportedContexts)) {
            if (file_exists('build/component/layoutcontexts')) {
                $data = unserialize(file_get_contents('build/component/layoutcontexts'));
                self::$_supportedContexts = $data['contexts'];
                self::$_supportedChildContexts = $data['childContexts'];
            } else {
                self::$_supportedContexts = array();
                self::$_supportedChildContexts = array();
            }
        }
    }

    public final function getSupportedContexts()
    {
        $cacheId = 'layout-ctx-'.$this->_class;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if (!$success) {
            self::_loadFromBuild();
            if (!isset(self::$_supportedContexts[$this->_class])) {
                self::$_supportedContexts[$this->_class] = $this->calcSupportedContexts();
            }
            $ret = self::$_supportedContexts[$this->_class];
            Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        }
        return $ret;
    }

    public final function getSupportedChildContexts($generator)
    {
        $cacheId = 'layout-childctx-'.$this->_class.'-'.$generator;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if (!$success) {
            self::_loadFromBuild();
            if (!isset(self::$_supportedChildContexts[$this->_class])) {
                self::$_supportedChildContexts[$this->_class] = $this->calcSupportedChildContexts();
            }
            if (self::$_supportedChildContexts[$this->_class] && isset(self::$_supportedChildContexts[$this->_class][$generator])) {
                $ret = self::$_supportedChildContexts[$this->_class][$generator];
            } else {
                $ret = false;
            }
            Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        }
        return $ret;
    }

    public function calcSupportedContexts()
    {
        return false;
    }

    public function calcSupportedChildContexts()
    {
        return false;
    }

    public function getChildContexts(Kwf_Component_Data $data, Kwf_Component_Data $child)
    {
        return $this->getContexts($data);
    }

    public function getContexts(Kwf_Component_Data $data)
    {
        if ($data->isPage || isset($data->box)) {
            $componentWithMaster = Kwf_Component_View_Helper_Master::getComponentsWithMasterTemplate($data);
            $last = array_pop($componentWithMaster);
            if ($last && $last['type'] == 'master') {
                $p = $last['data'];
            } else {
                $p = Kwf_Component_Data_Root::getInstance(); // for tests
            }
            return Kwf_Component_MasterLayout_Abstract::getInstance($p->componentClass)->getContexts($data);
        } else {
            $parent = $data->parent;
            if (!$parent) {
                throw new Kwf_Exception("Can't detect contexts");
            }
            return Kwf_Component_Layout_Abstract::getInstance($parent->componentClass)->getChildContexts($parent, $data);
        }
    }

    /**
     * Returns the contentWidth of a given child
     *
     * Can be overridden to adapt the available child width
     *
     * Use 'contentWidthSubtract' setting to subtract a fixed amount
     * from getContentWidth() value
     *
     * @return int
     */
    public function getChildContentWidth(Kwf_Component_Data $data, Kwf_Component_Data $child)
    {
        $ret = $this->getContentWidth($data);
        if ($this->_hasSetting('contentWidthSubtract')) {
            $ret -= $this->_getSetting('contentWidthSubtract');
        }
        return $ret;
    }

    public function getContentWidth(Kwf_Component_Data $data)
    {
        if ($this->_hasSetting('contentWidth')) return $this->_getSetting('contentWidth');

        if ($data->isPage || isset($data->box)) {
            $componentWithMaster = Kwf_Component_View_Helper_Master::
                getComponentsWithMasterTemplate($data);
            $last = array_pop($componentWithMaster);
            if ($last && $last['type'] == 'master') {
                $p = $last['data'];
            } else {
                $p = Kwf_Component_Data_Root::getInstance(); // for tests
            }
            return Kwf_Component_MasterLayout_Abstract::getInstance($p->componentClass)->getContentWidth($data);
        } else {
            if (!$data->parent) {
                throw new Kwf_Exception("Can't detect contentWidth, use contentWidth setting for '".$data->componentClass."'");
            }
            return self::getInstance($data->parent->componentClass)->getChildContentWidth($data->parent, $data);
        }
    }
}
