<?php
/**
 * Vivid Planet Component (Vpc)
 * @package Vpc
 * @copyright Copyright (c) 2007, Vivid Planet Software GmbH
 */
abstract class Vpc_Abstract extends Vpc_Master_Abstract
{
    protected $_row;
    private $_pdfWriter;
    const LOREM_IPSUM = 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.';

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
        return array_unique($ret);
    }

    public static function getRecursiveChildComponentClasses($class, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }

        $countOutput = '';
        foreach (debug_backtrace() as $bt) {
            if ($bt['function'] == '_formatChildConstraints') {
//                 $countOutput = '_formatChildConstraints<br />';
                break;
            }
        }
        if (!$countOutput) {
            foreach (debug_backtrace() as $bt) {
                if (isset($bt['class'])) $countOutput .= $bt['class'];
                $countOutput .= '::'.$bt['function']."<br />";
            }
        }
        $countOutput = "<b>$class</b><br />".$select->toDebug().$countOutput."<br />";

        $cacheId = serialize($select->getParts());
        $ret = self::_getRecursiveChildComponentClasses($class, $select, $cacheId);

        $countOutput .= "<b>result:</b><pre>".print_r($ret, true)."</pre></br>";
        Vps_Benchmark::count('getRecChildCClasses', $countOutput);
        return $ret;
    }

    private static function _getRecursiveChildComponentClasses($class, $select, $cacheId)
    {
        static $ccc = null;
        if (is_null($ccc)) {
            if (Vps_Registry::get('config')->debug->settingsCache) {
                $cache = new Vps_Assets_Cache(array('checkComponentSettings' => true));
                $cacheFileId = 'rccc';
                $ccc = $cache->load($cacheFileId);
                if (!$ccc) {
                    $benchmark = Vps_Benchmark::start('getRecursiveChildComponentClasses cache');
                    $ccc = array();
                    //�bliche aufrufe cachen: reihenfolge von wheres ist wichtig
                    foreach (Vpc_Abstract::getComponentClasses() as $c) {
                        self::getRecursiveChildComponentClasses($c, array('page'=>true));
                        self::getRecursiveChildComponentClasses($c, array('pseudoPage'=>true));
                        self::getRecursiveChildComponentClasses($c, array('showInMenu'=>true, 'page'=>true));
                        self::getRecursiveChildComponentClasses($c, array('page'=>false));
                        self::getRecursiveChildComponentClasses($c, array('inherit'=>true));
                        self::getRecursiveChildComponentClasses($c, array('page'=>false, 'unique'=>true, 'inherit'=>true));
                        self::getRecursiveChildComponentClasses($c, array('box'=>true));
                        self::getRecursiveChildComponentClasses($c, array('flags'=>array('noIndex'=>true), 'page'=>false));
                        self::getRecursiveChildComponentClasses($c, array('page'=>false, 'flags'=>array('processInput'=>true)));
                    }
                    $cache->save($ccc, $cacheFileId);
                    if ($benchmark) $benchmark->stop();
                }
            }
        }

        
        if (isset($ccc[$class.$cacheId])) {
            Vps_Benchmark::count('rccc cache hit');
            return $ccc[$class.$cacheId];
        }

        $childConstraints = array('page' => false);

        $childComponentClassesSelect = clone $select;
        $childComponentClassesSelect->unsetPart(Vps_Component_Select::SKIP_ROOT);
        $ccc[$class.$cacheId] = Vpc_Abstract::getChildComponentClasses($class, $childComponentClassesSelect);
        foreach (Vpc_Abstract::getChildComponentClasses($class, $childConstraints) as $childClass) {
            $classes = Vpc_Abstract::_getRecursiveChildComponentClasses($childClass, $select, $cacheId);
            if ($classes) {
                $ccc[$class.$cacheId][] = $childClass;
            }
//             $ccc[$class.$cacheId] = array_merge($ccc[$class.$cacheId], $classes);
        }
        $ccc[$class.$cacheId] = array_unique($ccc[$class.$cacheId]);
        
        $countOutput = '';
        foreach (debug_backtrace() as $bt) {
            if ($bt['function'] == 'getRecursiveChildComponents') {
//                 $countOutput = 'getRecursiveChildComponents<br />';
                break;
            }
        }
        
        if (!$countOutput) {
            foreach (debug_backtrace() as $bt) {
                if (isset($bt['class'])) $countOutput .= $bt['class'];
                $countOutput .= '::'.$bt['function'].'(';
                
                foreach ($bt['args'] as $arg) {
                    if (is_string($arg)) {
                        $countOutput .= $arg;
                    } else if ($arg instanceof Vps_Model_Select) {
                        $countOutput .= print_r($arg->getParts(), true);
                    } else if ($arg instanceof Vps_Component_Data) {
                        $countOutput .= $arg->componentId;
//                     } else if (is_array($arg)) {
//                         $countOutput .= substr(print_r($arg, true), 0, 20);
                    } else {
                        $countOutput .= '?';
                    }
                    $countOutput .= ', ';
                }
                $countOutput = substr($countOutput, 0, -2);
                $countOutput .= ")<br />";
            }
        }
        $countOutput = "<b>$class</b><br />".$select->toDebug().$countOutput."";
        $countOutput .= "<b>result:</b><pre>".print_r($ccc[$class.$cacheId], true)."</pre><br/>";
        Vps_Benchmark::count('rccc cache miss', $countOutput);


        return $ccc[$class.$cacheId];
    }

    public static function getChildComponentClass($class, $generator, $componentKey = null)
    {
        $constraints = array(
            'generator' => $generator,
            'componentKey' => $componentKey
        );
        $classes = self::getChildComponentClasses($class, $generator);
        if (!isset($classes[0])) {
            throw new Vps_Exception("childComponentClass '$componentKey' for generator '$generator' not set for '$class'");
        }
        return $classes[0];
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

    public function getSearchVars()
    {
        return array('text' => '');
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

    /**
     * Gibt Werte für den Statistik-Decorator zurück
     * 
     * Standardmäßig wird in die Tabelle "temp" geschrieben,
     * falls man in eine andere Tabelle schreiben möchte, ist der Tabellenname
     * als Schlüssel für das eigentlich Wertearray anzugebn.
     * 
     * Falls kein leeres Array zurückgegeben wird, wird für die aktuelle Seite die
     * Statistik gezählt, falls nicht ohnehin die Statistik generell aktiviert
     * ist.
     * 
     * Falls die Statistik generell aktiviert ist, werden die hier angegebenen
     * Variable am Ende gemergt.
     *
     * @return Array mit Statistik-Variablen
     */
     public function getStatisticVars()
     {
         return array();
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
            foreach ($process as $i) {
                $i->getComponent()->processInput($_POST);
            }
            echo Vps_View_Component::renderComponent($this->getData(), null, true);
        }
    }
    /**
     * Gibt die Variablen für View zurück.
     *
     * @return array Template-Variablen
     */
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['placeholder'] = $this->_getSetting('placeholder');

        $cssClass = array($this->_formatCssClass(get_class($this)));
        $dirs = explode(PATH_SEPARATOR, get_include_path());
        $c = get_parent_class($this);
        do {
            $file = str_replace('_', '/', $c);
            if (substr($file, -10) != '/Component') {
                $file .= '/Component';
            }
            $file .= '.css';
            foreach ($dirs as $dir) {
                if (is_file($dir . '/' . $file)) {
                    $cssClass[] = $this->_formatCssClass($c);
                    break;
                }
            }
        } while($c = get_parent_class($c));
        $ret['cssClass'] = implode(' ', array_reverse($cssClass));
        if (Vpc_Abstract::hasSetting(get_class($this), 'cssClass')) {
            $ret['cssClass'] .= ' '.Vpc_Abstract::getSetting(get_class($this), 'cssClass');
            $ret['cssClass'] = trim($ret['cssClass']);
        }
        $ret['data'] = $this->getData();
        return $ret;
    }
    private function _formatCssClass($cls)
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

