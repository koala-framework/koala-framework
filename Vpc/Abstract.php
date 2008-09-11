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
    }
    
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
        $ret['isPdf'] = false;
        return $ret;
    }

    public static function getChildComponentClasses($class, $select = array())
    {
        if (is_string($select)) {
            $select = array('generator' => $select);
        }
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $ret = array();
        $generators = Vps_Component_Generator_Abstract::getInstances($class, $select);
        if (!$generators) {
            return $ret;
        }
        foreach ($generators as $generator) {
            $ret = array_merge($ret, $generator->getChildComponentClasses($select));
        }
        return array_unique(array_values($ret));
    }

    public static function getIndirectChildComponentClasses($class, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $cacheId = serialize($select->getParts());
        $ret = self::_getIndirectChildComponentClasses($class, $select, $cacheId);
        return $ret;
    }

    private static function _getIndirectChildComponentClasses($class, $select, $cacheId)
    {
        static $ccc = null;
        if (is_null($ccc)) {
            if (Vps_Registry::get('config')->debug->settingsCache) {
                $cache = new Vps_Assets_Cache(array('checkComponentSettings' => true));
                $cacheFileId = 'rccc';
                $ccc = $cache->load($cacheFileId);
                if (!$ccc) {
                    $benchmark = Vps_Benchmark::start('getIndirectChildComponentClasses cache');
                    $ccc = array();
                    //übliche aufrufe cachen: reihenfolge von wheres ist wichtig
                    foreach (Vpc_Abstract::getComponentClasses() as $c) {
                        self::getIndirectChildComponentClasses($c, array('page'=>true));
                        self::getIndirectChildComponentClasses($c, array('pseudoPage'=>true));
                        self::getIndirectChildComponentClasses($c, array('showInMenu'=>true, 'page'=>true));
                        self::getIndirectChildComponentClasses($c, array('page'=>false));
                        self::getIndirectChildComponentClasses($c, array('inherit'=>true));
                        self::getIndirectChildComponentClasses($c, array('page'=>false, 'unique'=>true, 'inherit'=>true));
                        self::getIndirectChildComponentClasses($c, array('box'=>true));
                        self::getIndirectChildComponentClasses($c, array('flags'=>array('noIndex'=>true), 'page'=>false));
                        self::getIndirectChildComponentClasses($c, array('page'=>false, 'flags'=>array('processInput'=>true)));
                        self::getIndirectChildComponentClasses($c, array('page'=>false, 'flags'=>array('metaTags'=>true)));
                        self::getIndirectChildComponentClasses($c, array('showInMenu'=>true, 'type'=>'main', 'page' => true));
                    }
                    $cache->save($ccc, $cacheFileId);
                    if ($benchmark) $benchmark->stop();
                }
            }
        }

        if (isset($ccc[$class.$cacheId])) {
            Vps_Benchmark::count('iccc cache hit');
            return $ccc[$class.$cacheId];
        }
        Vps_Benchmark::count('iccc cache miss');

        $childConstraints = array('page' => false);

        $childComponentClassesSelect = clone $select;
        $childComponentClassesSelect->unsetPart(Vps_Component_Select::SKIP_ROOT);
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
        return $ccc[$class.$cacheId];
    }

    public static function getChildComponentClass($class, $generator, $componentKey = null)
    {
        $constraints = array(
            'generator' => $generator,
            'componentKey' => $componentKey
        );
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

    protected function _getRow()
    {
        if (!isset($this->_row)) {
            $table = $this->getTable();
            if ($table && !isset($this->_row)) {
                $info = $table->info();
                if ($info['primary'] == array(1 => 'component_id')) {
                    $this->_row = $table->findRow($this->getDbId());
                }
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

    public function sendContent($decoratedPage)
    {
        if (isset($_GET['pdf']) && ($pdfClass = Vpc_Admin::getComponentFile(get_class($this), 'Pdf', 'php', true))) {
            //TODO: bessere lösung für das!
            $masterClass = Vpc_Admin::getComponentFile(get_class($this), 'PdfMaster', 'php', true);
            if (!$masterClass) { $masterClass = 'Vps_Pdf_TcPdf'; }
            $pdf = new $masterClass($this);
            $this->getPdfWriter($pdf)->writeContent();
            $pdf->output();
            die();
        } else {
            header('Content-Type: text/html; charset=utf-8');
            
            $process = $this->getData()
                ->getRecursiveChildComponents(array(
                        'page' => false,
                        'flags' => array('processInput' => true)
                    ));
            if (Vps_Component_Abstract::getFlag(get_class($this), 'processInput')) {
                $process[] = $this->getData();
            }

            $postData = $_REQUEST;
            //in _REQUEST sind _FILES nicht mit drinnen
            foreach ($_FILES as $k=>$file) {
                if (is_array($file['tmp_name'])) {
                    //wenn name[0] dann kommts in komischer form daher -> umwandeln
                    foreach (array_keys($file['tmp_name']) as $i) {
                        foreach (array_keys($file) as $prop) {
                            $postData[$k][$i][$prop] = $file[$prop][$i];
                        }
                    }
                } else {
                    $postData[$k] = $file;
                }
            }
            foreach ($process as $i) {
                if (method_exists($i->getComponent(), 'processInput')) {
                    $i->getComponent()->processInput($postData);
                }
            }
            Vps_Component_Cache::getInstance()->process(false);

            echo Vps_View_Component::renderMasterComponent($this->getData());

            foreach ($process as $i) {
                if (method_exists($i->getComponent(), 'postProcessInput')) {
                    $i->getComponent()->postProcessInput($postData);
                }
            }
            Vps_Component_Cache::getInstance()->process();
        }
    }
    
    /**
     * Gibt die Variablen für View zurück.
     *
     * @return array Template-Variablen
     */
    public function getTemplateVars()
    {
        $ret = array();
        $ret['placeholder'] = $this->_getSetting('placeholder');
        $ret['cssClass'] = self::getCssClass($this);
        $ret['data'] = $this->getData();
        return $ret;
    }

    static public function getCssClass($component)
    {
        if (!is_string($component)) $component = get_class($component);

        $ret = '';
        if (Vpc_Abstract::hasSetting($component, 'cssClass')) {
            $ret .= Vpc_Abstract::getSetting($component, 'cssClass').' ';
        }

        $cssClass = array(self::_formatCssClass($component));
        $dirs = explode(PATH_SEPARATOR, get_include_path());
        foreach (self::getParentClasses($component) as $c) {
            $file = str_replace('_', '/', $c);
            if (substr($file, -10) != '/Component') {
                $file .= '/Component';
            }
            $file .= '.css';
            foreach ($dirs as $dir) {
                if (is_file($dir . '/' . $file)) {
                    $cssClass[] = self::_formatCssClass($c);
                    break;
                }
            }
        }
        $ret .= implode(' ', array_reverse($cssClass));
        return trim($ret);
    }

    static private function _formatCssClass($cls)
    {
        if (substr($cls, -10) == '_Component') {
            $cls = substr($cls, 0, -10);
        }
        $cls = str_replace('_', '', $cls);
        return strtolower(substr($cls, 0, 1)) . substr($cls, 1);
    }

    static public function getShortcutUrl($componentClass, Vps_Component_Data $data)
    {
        if (!Vpc_Abstract::hasSetting($componentClass, 'shortcutUrl')) {
            throw new Vps_Exception("You must either have the setting 'shortcutUrl' or reimplement getShortcutUrl method for '$componentClass'");
        }
        return Vpc_Abstract::getSetting($componentClass, 'shortcutUrl');
    }

    static public function getDataByShortcutUrl($componentClass, $url)
    {
        if (!Vpc_Abstract::hasSetting($componentClass, 'shortcutUrl')) {
            throw new Vps_Exception("You must either have the setting 'shortcutUrl' or reimplement getDataByShortcutUrl method for '$componentClass'");
        }

        $sc = Vpc_Abstract::getSetting($componentClass, 'shortcutUrl');
        if (substr($url, 0, strlen($sc)) != $sc) return false;
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass($componentClass);
        if ($url != $sc) {
            $ret = $ret->getChildPageByPath(substr($url, strlen($sc)+1));
        }
        return $ret;
    }

    public static function getComponentClassesByParentClass($class)
    {
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            if ($c == $class) {
                $ret[] = $c;
                continue;
            }
            foreach (Vpc_Abstract::getParentClasses($c) as $p) {
                if ($p == $class) {
                    $ret[] = $c;
                    break;
                }
            }
        }
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
}

