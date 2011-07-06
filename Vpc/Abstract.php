<?php
/**
 * Vivid Planet Component (Vpc)
 * @package Vpc
 * @copyright Copyright (c) 2007, Vivid Planet Software GmbH
 */
abstract class Vpc_Abstract extends Vps_Component_Abstract
{
    private $_data;
    protected $_row;
    private $_pdfWriter;
    const LOREM_IPSUM = 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.';

    public function __construct(Vps_Component_Data $data)
    {
        $this->_data = $data;
        parent::__construct();
        Vps_Benchmark::count('components', $data->componentClass.' '.$data->componentId);
    }

    /**
     * @return Vps_Component_Data
     */
    public function getData()
    {
        return $this->_data;
    }

    public function getDbId()
    {
        return $this->getData()->dbId;
    }

    public function getComponentId()
    {
        return $this->getData()->componentId;
    }

    protected function _getParam($param)
    {
        return isset($_REQUEST[$param]) ? $_REQUEST[$param] : null;
    }

    /**
     * Shortcut, fragt vom Seitenbaum die Url für eine Komponente ab
     *
     * @return string URL der Seite
     */
    public function getUrl()
    {
        return $this->getData()->getPage()->url;
    }

    public function getName()
    {
        return $this->getData()->getPage()->name;
    }

    /**
     * Shortcut, fragt vom Seitenbaum, ob die unsichtbaren Einträge
     * auch angezeige werden
     *
     * @return boolean
     */
    protected function _showInvisible()
    {
        return Vps_Registry::get('config')->showInvisible;
    }

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = true;
        $ret['allowIsolatedRender'] = false;
        return $ret;
    }

    public static function getChildComponentClasses($class, $select = array())
    {
        if (is_string($select)) {
            $g = Vpc_Abstract::getSetting($class, 'generators');
            $ret = $g[$select]['component'];
            if (!is_array($ret)) $ret = array($select => $ret);
            foreach ($ret as $k=>$i) {
                if (!$i) unset($ret[$k]);
            }
            return $ret;
        } else if (!$select) {
            $ret = array();
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $g) {
                if (is_array($g['component'])) {
                    foreach ($g['component'] as $c) {
                        if ($c) $ret[] = $c;
                    }
                } else if ($g['component']) {
                    $ret[] = $g['component'];
                }
            }
            return array_unique($ret);
        } else if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $ret = array();
        $generators = Vps_Component_Generator_Abstract::getInstances($class, $select);
        if (!$generators) {
            return $ret;
        }
        foreach ($generators as $generator) {
            $c = $generator->getChildComponentClasses($select);
            if (!$select->hasPart(Vps_Component_Select::WHERE_GENERATOR)) {
                $c = array_values($c);
            }
            $ret = array_merge($ret, $c);
        }
        if (!$select->hasPart(Vps_Component_Select::WHERE_GENERATOR)) {
            $ret = array_unique($ret);
        }
        return $ret;
    }

    public static function getIndirectChildComponentClasses($class, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $cacheId = $select->getHash();
        $ret = self::_getIndirectChildComponentClasses($class, $select, $cacheId);
        return $ret;
    }

    private static function _getIndirectChildComponentClasses($class, $select, $cacheId)
    {
        static $ccc = array();

        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
        $currentCacheId = $prefix.'iccc-'.md5($class.$cacheId);

        if (isset($ccc[$class.$cacheId])) {
            Vps_Benchmark::count('iccc cache hit');
            return $ccc[$class.$cacheId];
        }
        $ret = apc_fetch($cacheId, $success);
        if ($success) {
            $ccc[$class.$cacheId] = $ret;
            Vps_Benchmark::count('iccc cache semi-hit');
            return $ret;
        }

        Vps_Benchmark::count('iccc cache miss', $class.' '.print_r($select->getParts(), true));
        $childConstraints = array('page' => false);
        $ccc[$class.$cacheId] = array();
        foreach (Vpc_Abstract::getChildComponentClasses($class, $childConstraints) as $childClass) {
            if (Vpc_Abstract::getChildComponentClasses($childClass, $select, $cacheId)) {
                $ccc[$class.$cacheId][] = $childClass;
                continue;
            }
            $classes = Vpc_Abstract::_getIndirectChildComponentClasses($childClass, $select, $cacheId);
            if ($classes) {
                $ccc[$class.$cacheId][] = $childClass;
            }
        }
        $ccc[$class.$cacheId] = array_unique(array_values($ccc[$class.$cacheId]));

        apc_add($currentCacheId, $ccc[$class.$cacheId]);
        return $ccc[$class.$cacheId];

    }

    public static function getChildComponentClass($class, $generator, $componentKey = null)
    {
        $constraints = array(
            'generator' => $generator,
        );
        if ($componentKey) $constraints['componentKey'] = $componentKey;
        $classes = array_values(self::getChildComponentClasses($class, $constraints));
        if (!isset($classes[0])) {
            throw new Vps_Exception("childComponentClass '$componentKey' for generator '$generator' not set for '$class'");
        }
        return $classes[0];
    }

    public static function hasChildComponentClass($class, $generator, $componentKey = null)
    {
        $constraints = array(
            'generator' => $generator,
            'componentKey' => $componentKey
        );
        $classes = self::getChildComponentClasses($class, $constraints);
        return isset($classes[0]);
    }

    public function getRow()
    {
        return $this->_getRow();
    }

    protected function _getRow()
    {
        if (!isset($this->_row)) {
            $model = $this->getModel();
            if (!$model) return null;
            if ($model instanceof Vps_Model_Interface) {

                $dbId = $this->getDbId();
                $sharedDataClass = self::getFlag($this->getData()->componentClass, 'sharedDataClass');
                if ($sharedDataClass) {
                    $component = $this->getData();
                    while ($component) {
                        if (is_instance_of($component->componentClass, $sharedDataClass))
                            $dbId = $component->dbId;
                        $component = $component->parent;
                    }
                }

                $this->_row = $model->getRow($dbId);
                if (!$this->_row) {
                    $this->_row = $model->createRow();
                    $this->_row->component_id = $dbId;
                }
            } else {
                $this->_row = $model->find($this->getDbId())->current();
            }
        }
        return $this->_row;
    }

    /**
     * Gibt an, ob eine Komponente Inhalt hat
     *
     * Wird verwendet in Templates um zu prüfen, ob eine Komponente einen Inhalt
     * hat (z.B. Text oder Download)
     *
     * @return boolean $hasContent Ob die Komponente Inhalt hat (true) oder nicht (false)
     */
    public function hasContent()
    {
        return true;
    }

    public function getPdfWriter($pdf)
    {
        if (!isset($this->_pdfWriter)) {
            $class = Vpc_Admin::getComponentFile(get_class($this), 'Pdf', 'php', true);
            $this->_pdfWriter = new $class($this, $pdf);
        }
        return $this->_pdfWriter;
    }

    private function _getRequestWithFiles()
    {
        $ret = $_REQUEST;
        //in _REQUEST sind _FILES nicht mit drinnen
        foreach ($_FILES as $k=>$file) {
            if (is_array($file['tmp_name'])) {
                //wenn name[0] dann kommts in komischer form daher -> umwandeln
                foreach (array_keys($file['tmp_name']) as $i) {
                    foreach (array_keys($file) as $prop) {
                        $ret[$k][$i][$prop] = $file[$prop][$i];
                    }
                }
            } else {
                $ret[$k] = $file;
            }
        }
        return $ret;
    }

    protected function _callProcessInput()
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
        $cacheId = $prefix.'procI-'.$this->getData()->getPageOrRoot()->componentId;
        $processCached = apc_fetch($cacheId, $success);
        if (!$success) {
            $process = $this->getData()
                ->getRecursiveChildComponents(array(
                        'page' => false,
                        'flags' => array('processInput' => true)
                    ));
            if (Vps_Component_Abstract::getFlag($this->getData()->componentClass, 'processInput')) {
                $process[] = $this->getData();
            }

            // TODO: Äußerst suboptimal
            if ($this instanceof Vpc_Show_Component) {
                $process += $this->getShowComponent()
                    ->getRecursiveChildComponents(array(
                        'page' => false,
                        'flags' => array('processInput' => true)
                    ));
                if (Vps_Component_Abstract::getFlag(get_class($this->getShowComponent()->getComponent()), 'processInput')) {
                    $process[] = $this->getData();
                }
            }
            $datas = array();
            foreach ($process as $p) {
                $datas[] = $p->vpsSerialize();
            }
            apc_add($cacheId, $datas);
        } else {
            $process = array();
            foreach ($processCached as $d) {
                $process[] = Vps_Component_Data::vpsUnserialize($d);
            }
        }

        $postData = $this->_getRequestWithFiles();
        foreach ($process as $i) {
            Vps_Benchmark::count('processInput', $i->componentId);
            if (method_exists($i->getComponent(), 'preProcessInput')) {
                $i->getComponent()->preProcessInput($postData);
            }
        }
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'processInput')) {
                $i->getComponent()->processInput($postData);
            }
        }
        if (class_exists('Vps_Component_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Vps_Component_ModelObserver::getInstance()->process(false);
        }
        return $process;
    }

    protected function _callPostProcessInput($process)
    {
        $postData = $this->_getRequestWithFiles();
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'postProcessInput')) {
                $i->getComponent()->postProcessInput($postData);
            }
        }
        if (class_exists('Vps_Component_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Vps_Component_ModelObserver::getInstance()->process();
        }
    }

    public function sendContent($renderMaster = true)
    {
        header('Content-Type: text/html; charset=utf-8');
        $process = $this->_callProcessInput();
        Vps_Benchmark::checkpoint('processInput');
        $useCache = !Vps_Registry::get('config')->debug->componentCache->disable;
        echo $this->getData()->render($useCache, $renderMaster);
        Vps_Benchmark::checkpoint('render');
        $this->_callPostProcessInput($process);
        Vps_Benchmark::checkpoint('postProcessInput');
    }

    /**
     * Gibt die Variablen für View zurück.
     *
     * @return array Template-Variablen
     */
    public function getTemplateVars()
    {
        $ret = array();
        $ret['placeholder'] = $this->_getPlaceholder();
        $ret['cssClass'] = self::getCssClass($this);
        $ret['data'] = $this->getData();
        $ret['row'] = $this->_getRow();
        return $ret;
    }

    protected function _getPlaceholder($placeholder = null)
    {
        $ret = $this->_getSetting('placeholder');
        if ($placeholder) {
            return $this->getData()->trlStaticExecute($ret[$placeholder]);
        }
        foreach ($ret as $k => $v) {
            $ret[$k] = $this->getData()->trlStaticExecute($v);
        }
        return $ret;
    }

    /**
     * Data i.e. for json or xml output
     */
    public function getExportData()
    {
        throw new Vps_Exception_NotYetImplemented("getExportData is not yet implemented for component '".get_class($this)."'");
    }

    public function getMailVars($user = null)
    {
        return $this->getTemplateVars();
    }

    // deprecated
    public function getCacheVars() {
        throw new Vps_Exception('getCacheVars is not supported anymore.');
    }

    public static function getStaticCacheVars() {
        throw new Vps_Exception('getStaticCacheVars is not supported anymore.');
    }

    public function getCacheMeta()
    {
        return array();
    }

    public static function getStaticCacheMeta($componentClass) {
        $ret = array();
        if (Vpc_Abstract::hasSetting($componentClass, 'ownModel')) {
            $ret[] = new Vps_Component_Cache_Meta_Static_OwnModel();
        }
        return $ret;
    }

    public function onCacheCallback($row) {}

    public static function getTemplateFile($componentClass, $filename = 'Component')
    {
        return Vpc_Admin::getComponentFile($componentClass, $filename, 'tpl');
    }

    static public function getCssClass($component)
    {
        if (!is_string($component)) $component = $component->getData()->componentClass;
        return self::getSetting($component, 'processedCssClass');
    }

    static public function getShortcutUrl($componentClass, Vps_Component_Data $data)
    {
        if (!Vpc_Abstract::hasSetting($componentClass, 'shortcutUrl')) {
            throw new Vps_Exception("You must either have the setting 'shortcutUrl' or reimplement getShortcutUrl method for '$componentClass'");
        }
        return Vpc_Abstract::getSetting($componentClass, 'shortcutUrl');
    }

    public static function getDataByShortcutUrl($componentClass, $url)
    {
        if (!Vpc_Abstract::hasSetting($componentClass, 'shortcutUrl')) {
            throw new Vps_Exception("You must either have the setting 'shortcutUrl' or reimplement getDataByShortcutUrl method for '$componentClass'");
        }
        $sc = Vpc_Abstract::getSetting($componentClass, 'shortcutUrl');
        $parts = explode('/', $url);
        $constraints = array();
        $isDomain = is_instance_of(
            Vps_Component_Data_Root::getInstance()->componentClass,
           'Vpc_Root_DomainRoot_Component'
        );
        if ($isDomain) {
            $pos = strpos($url, '/', 1);
            $domain = substr($url, 0, $pos);
            $url = substr($url, $pos + 1);
        }
        $shortcut = substr($url, 0, strpos($url, '/', 1));
        if ($shortcut != $sc) return false;
        if ($isDomain) {
            $components = Vps_Component_Data_Root::getInstance()->
                getComponentsByClass('Vpc_Root_DomainRoot_Domain_Component', array('id' => '-' . $domain));
            foreach ($components as $c) {
                if ($c->row->id == $domain) $constraints = array('subroot' => $c);
            }
        }
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentBySameClass($componentClass, $constraints);
        if ($component) {
            return $component->getChildPageByPath(substr($url, strlen($sc) + 1));
        }
        return false;
    }

    public static function getComponentClassesByParentClass($class)
    {
        if (!is_array($class)) $class = array($class);

        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
        $cacheId = $prefix.'cclsbpc-'.implode('-', $class);
        $ret = apc_fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            if (in_array($c, $class) || in_array((strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c), $class)) {
                $ret[] = $c;
                continue;
            }
            foreach (Vpc_Abstract::getParentClasses($c) as $p) {
                if (in_array($p, $class)) {
                    $ret[] = $c;
                    break;
                }
            }
        }
        apc_add($cacheId, $ret);
        return $ret;
    }
    public static function getComponentClassByParentClass($class)
    {
        $ret = self::getComponentClassesByParentClass($class);
        if (count($ret) != 1) {
            if (!$ret) {
                throw new Vps_Exception("No Component with class '$class' found");
            }
            throw new Vps_Exception("More then one component with class '$class' found, there should exist only one");
        }
        return $ret[0];
    }

    public function getViewCacheLifetime()
    {
        return null;
    }

    public function getViewCacheSettings()
    {
        return array(
            'enabled' => $this->_getSetting('viewCache'),
            'lifetime' => $this->getViewCacheLifetime()
        );
    }
}

