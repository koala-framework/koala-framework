<?php
abstract class Kwf_Component_MasterLayout_Abstract
{
    protected $_class;
    protected $_settings;
    private static $_supportedContexts;
    private static $_supportedBoxContexts;

    public function __construct($class, array $settings)
    {
        $this->_class = $class;
        $this->_settings = $settings;
        $this->_init();
    }

    protected function _init()
    {
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
            if (!Kwc_Abstract::hasSetting($class, 'masterLayout')) {
                //default masterLayout
                $layout = array(
                    'class' => 'Kwf_Component_MasterLayout_Legacy'
                );
            } else {
                $layout = Kwc_Abstract::getSetting($class, 'masterLayout');
            }
            $layoutClass = $layout['class'];
            unset($layout['class']);
            $i[$class] = new $layoutClass($class, $layout);
        }
        return $i[$class];
    }

    abstract public function getContexts(Kwf_Component_Data $data);
    abstract public function getContentWidth(Kwf_Component_Data $data);

    /**
     * @internal
     */
    public static function _buildAll($componentClasses)
    {
        $masterLayouts = "\$all-master-layouts: ();\n";
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (Kwc_Abstract::hasSetting($c, 'masterLayout')) {
                $masterLayout = Kwc_Abstract::getSetting($c, 'masterLayout');
                $f = new Kwf_Assets_Dependency_File($masterLayout['layoutConfig']);
                $masterLayouts .= $f->getContents(null)."\n";
                $masterLayouts .= "\$all-master-layouts: map-merge(\$all-master-layouts, \$master-layouts);\n";
            }
        }
        $masterLayouts .= "\$master-layouts: \$all-master-layouts;\n";
        $masterLayouts .= "\$all-master-layouts: null\n";

        $file = "cache/scss/generated/config/_master-layouts.scss";
        if (!is_dir(dirname($file))) mkdir(dirname($file), 0777, true);
        if (!file_exists($file) || file_get_contents($file) != $masterLayouts) { //only modify if actually changed
            file_put_contents($file, $masterLayouts);
        }


        foreach ($componentClasses as $cmp) {
            if (Kwc_Abstract::hasSetting($cmp, 'masterLayout')) {
                self::getInstance($cmp)->_build();
            }
        }

        foreach ($componentClasses as $cmp) {
            if (Kwc_Abstract::hasSetting($cmp, 'masterLayout')) {
                //fills $_supportedContexts and $_supportedBoxContexts
                self::getInstance($cmp)->getSupportedContexts();
                self::getInstance($cmp)->_getSupportedBoxesContexts();
            }
        }
        $data = array(
            'contexts' => self::$_supportedContexts,
            'childContexts' => self::$_supportedBoxContexts,
        );

        file_put_contents('build/component/masterlayoutcontexts', serialize($data));
    }

    protected function _build()
    {
    }

    private static function _loadFromBuild()
    {
        if (!isset(self::$_supportedContexts)) {
            if (file_exists('build/component/masterlayoutcontexts')) {
                $data = unserialize(file_get_contents('build/component/masterlayoutcontexts'));
                self::$_supportedContexts = $data['contexts'];
                self::$_supportedBoxContexts = $data['boxContexts'];
            } else {
                self::$_supportedContexts = array();
                self::$_supportedBoxContexts = array();
            }
        }
    }
    public final function getSupportedContexts()
    {
        $cacheId = 'mlayout-ctx-'.$this->_class;
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

    private function _getSupportedBoxesContexts()
    {
        self::_loadFromBuild();
        if (!isset(self::$_supportedBoxContexts[$this->_class])) {
            self::$_supportedBoxContexts[$this->_class] = $this->calcSupportedBoxContexts();
        }
        return self::$_supportedBoxContexts[$this->_class];
    }

    public final function getSupportedBoxContexts($boxName)
    {
        $cacheId = 'mlayout-boxctx-'.$this->_class.'-'.$boxName;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if (!$success) {
            $boxesContexts = $this->_getSupportedBoxesContexts();
            $ret = $boxesContexts[$boxName];
            Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        }
        return $ret;
    }

    public function calcSupportedContexts()
    {
        return false;
    }

    public function calcSupportedBoxContexts()
    {
        return false;
    }
}
