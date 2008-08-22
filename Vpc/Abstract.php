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
    
    public static function getChildComponentClasses($class, $constraints = array())
    {
        $ret = array();
        if (is_string($constraints)) {
            $constraints = array('generator' => $constraints);
        }
        $generators = Vps_Component_Generator_Abstract::getInstances($class, $constraints);
        foreach ($generators as $generator) {
            $ret = array_merge($ret, $generator->getChildComponentClasses($constraints));
        }

        return array_unique($ret);
    }

    public static function getRecursiveChildComponentClasses($class, $constraints = array())
    {
        $cacheId = serialize($constraints);
        return self::_getRecursiveChildComponentClasses($class, $constraints, $cacheId);
    }
    
    private static function _getRecursiveChildComponentClasses($class, $constraints, $cacheId)
    {
        static $ccc = array();
        if (isset($ccc[$class.$cacheId])) {
            return $ccc[$class.$cacheId];
        }

        $childConstraints = array('page' => false);
        $ccc[$class.$cacheId] = Vpc_Abstract::getChildComponentClasses($class, $constraints);
        foreach (Vpc_Abstract::getChildComponentClasses($class, $childConstraints) as $childClass) {
            $ccc[$class.$cacheId] = array_merge(
                $ccc[$class.$cacheId], 
                Vpc_Abstract::_getRecursiveChildComponentClasses($childClass, $constraints, $cacheId)
            );
        }
        return array_unique($ccc[$class.$cacheId]);
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
            $masterClass = Vpc_Admin::getComponentFile(get_class($this), 'PdfMaster', 'php', true);
            if (!$masterClass) { $masterClass = 'Vps_Pdf_TcPdf'; }
            $pdf = new $masterClass($this);
            $this->getPdfWriter($pdf)->writeContent();
            $pdf->output();
            die();
        } else {
            header('Content-Type: text/html; charset=utf-8');
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

