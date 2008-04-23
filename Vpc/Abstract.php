<?php
/**
 * Vivid Planet Component (Vpc)
 * @package Vpc
 * @copyright Copyright (c) 2007, Vivid Planet Software GmbH
 */
abstract class Vpc_Abstract implements Vpc_Interface
{
    private $_treeCacheRow;

    private $_store;
    protected $_row;
    private $_tables = array();

    private $_pdfWriter;

    protected $_table;

    const LOREM_IPSUM = 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.';

    /**
     * Sollte nicht direkt aufgerufen werden, sondern über statische Methoden der Klasse. Kann nicht
     * überschrieben werden, stattdessen sollte setup() verwendet werden.
     *
     * @see createInstance
     * @see createPage
     *
     * @param Vps_Dao DAO
     * @param int Falls Komponenten geschachtelt werden, die componentId der obersten Komponente
     * @param int Id der aktuellen Komponente
     * @param string Falls dynamische Unterseite
     * @param string Falls dynamische Unterkomponente
     */
    public final function __construct(Vps_Dao_Row_TreeCache $treeCacheRow, $row = null)
    {
        $this->_treeCacheRow = $treeCacheRow;

        if ($row) {
            //vorübergehend für formular-felder
            foreach (Vpc_Abstract::getSetting(get_class($this), 'default') as $k=>$i) {
                if (!isset($row->$k)) $row->$k = $i;
            }
            $this->_row = $row;
        }

        $this->_init();

        if (Zend_Registry::isRegistered('infolog')) {
            if (!is_string($id)) $id = '(static)';
            Zend_Registry::get('infolog')->createComponent(get_class($this) . ' - ' . $id);
        }
    }
    
    /**
     * @return Vps_Dao_Row_TreeCache
     */
    public function getTreeCacheRow()
    {
        return $this->_treeCacheRow;
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
     * Wird nach dem Konstruktor aufgerufen. Initialisierungscode in Unterklassen ist hier richtig.
     */
    protected function _init()
    {
    }

    public function getDbId()
    {
        return $this->getTreeCacheRow()->db_id;
    }
    /**
     * @return DAO der Komponente
     */
    public function getDao()
    {
        return $this->getTreeCacheRow()->getTable()->getDao();
    }

    /**
     * Gibt die Variablen für View zurück.
     *
     * Variable 'template' muss immer gesetzt werden.
     *
     * @return array Template-Variablen
     */
    public function getTemplateVars()
    {
        $vars = array();
        $vars['assets']['js'] = array();
        $vars['assets']['css'] = array();
        $vars['class'] = get_class($this);
        //$vars['id'] = $this->getId();
        $vars['store'] = $this->_store;
        $vars['template'] = Vpc_Admin::getComponentFile(get_class($this), '', 'tpl');
        $vars['isOffline'] =
            isset($_SERVER['SERVER_NAME']) &&
            substr($_SERVER['SERVER_NAME'], -6) == '.vivid';
        if (!$vars['template']) {
            throw new Vpc_Exception(trlVps('Template not found for Component {0}',  get_class($this)));
        }
        $vars['placeholder'] = $this->_getSetting('placeholder');
        return $vars;
    }

    public function getSearchVars()
    {
        return array('text' => '');
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

    /**
     * Informationen über den Aufbau der aktuellen Komponente.
     *
     * Falls eine Komponente Unterkomponenten hat, deren Informationen
     * einschließen. Für jede Komponente wird im Array ein Eintrag mit
     * dem Schlüssel id und dem Wert Klassenname angehängt.
     *
     * @return array ComponentInfo
     */
    public function getComponentInfo()
    {
        return array($this->getId() => get_class($this));
    }
    
    public function getBoxVars($boxname)
    {
        $componentId = $this->getTreeCacheRow()->component_id . '-' . $boxname;
        $row = $this->getTreeCacheRow()->getTable()->find($componentId)->current();
        if ($row) {
            return $row->getComponent()->getTemplateVars();
        }
        return null;
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
        return $this->getTreeCacheRow()->url;
    }

    public function getName()
    {
        return $this->getTreeCacheRow()->name;
    }

    /**
     * Shortcut, fragt vom Seitenbaum, ob die unsichtbaren Einträge
     * auch angezeige werden
     *
     * @return boolean
     */
    protected function _showInvisible()
    {
        return $this->getTreeCacheRow()->getTable()->showInvisible();
    }

    /**
     * @deprecated
     */
    protected function showInvisible()
    {
        return $this->_showInvisible();
    }

    public function getTable($tablename = null)
    {
        if (!$tablename) {
            $tablename = $this->_getSetting('tablename');
            if (!$tablename) {
                return null;
            }
        }
        if (!isset($this->_tables[$tablename])) {
            $this->_tables[$tablename] = new $tablename(array('componentClass'=>get_class($this)));
        }
        return $this->_tables[$tablename];
    }

    public static function getSetting($class, $setting)
    {
        if (!Vps_Loader::classExists($class)) {
            $class = substr($class, 0, strrpos($class, '_')) . '_Component';
        }

        if (class_exists($class)) {
            $settings = call_user_func(array($class, 'getSettings'));
            return isset($settings[$setting]) ? $settings[$setting] : null ;
        } else {
            return null;
        }
    }

    public static function getSettings()
    {
        return array(
            'assets'        => array('files'=>array(), 'dep'=>array()),
            'assetsAdmin'   => array('files'=>array(), 'dep'=>array()),
            'componentIcon' => new Vps_Asset('paragraph_page'),
            'placeholder'   => array()
        );
    }

    protected function _getSetting($setting)
    {
        return self::getSetting(get_class($this), $setting);
    }

    protected function _getClassFromSetting($setting, $parentClass) {
        $classes = $this->_getSetting('childComponentClasses');
        if (!isset($classes[$setting])) {
            throw new Vpc_Exception(trlVps("ChildComponentClass {0} is not defined in settings.", $setting));
        }
        $class = $classes[$setting];
        if ($class != $parentClass && !is_subclass_of($class, $parentClass)) {
            throw new Vpc_Exception(trlVps("{0} '{1}' must be a subclass of {2}.",array($setting, $class, $parentClass)));
        }
        return $class;
    }

    public function store($key, $val)
    {
        $this->_store[$key] = $val;
    }

    public function getStore($key)
    {
        if (isset($this->_store[$key])) {
            return $this->_store[$key];
        } else {
            return null;
        }
    }

    public function onDelete()
    {
    }

    public function getPdfWriter($pdf)
    {
        if (!isset($this->_pdfWriter)) {
            $class = Vpc_Admin::getComponentFile(get_class($this), 'Pdf', 'php', true);
            $this->_pdfWriter = new $class($this, $pdf);
        }
        return $this->_pdfWriter;
    }

    public static function getComponentClasses($class = null)
    {
        static $componentClasses;
        if (!$class) {
            if ($componentClasses) return $componentClasses;
            $classes = array();
            $classes[] = 'Vpc_Root_Component';
            foreach (Zend_Registry::get('config')->vpc->pageClasses as $c) {
                if ($c->class && $c->text) {
                    $classes[] = $c->class;
                }
            }
            $componentClasses = array();
        } else {
            $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
            if (!is_array($classes)) return;
        }
        foreach ($classes as $class) {
            if ($class && !in_array($class, $componentClasses)) {
                $componentClasses[] = $class;
                self::getComponentClasses($class);
            }
        }
        return $componentClasses;
    }

    public function sendContent($decoratedPage)
    {
        header('Content-Type: text/html; charset=utf-8');

        $benchmark = Vps_Benchmark::getInstance();
        $benchmark->startSequence('Seitenbaum');

        $view = new Vps_View_Smarty();
        $view->url = $_SERVER['REQUEST_URI'];
        $view->component = $decoratedPage->getTemplateVars();

        $benchmark->stopSequence('Seitenbaum');
        $result = $benchmark->getResults();
        $view->time = sprintf("%01.2f", $result['Seitenbaum']['duration']/1.5);
        echo $view->render('');
    }

    protected function _getPlaceholder($name)
    {
        $s = $this->_getSetting('placeholder');
        if (!isset($s[$name])) {
            throw new Vps_Exception("Unknown placeholder '$name'");
        }
        return $s[$name];
    }
}

