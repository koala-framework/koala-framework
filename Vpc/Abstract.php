<?php
/**
 * Vivid Planet Component (Vpc)
 * @package Vpc
 * @copyright Copyright (c) 2007, Vivid Planet Software GmbH
 */
abstract class Vpc_Abstract extends Vps_Component_Abstract implements Vpc_Interface
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
    public function __construct(Vps_Dao_Row_TreeCache $treeCacheRow)
    {
        $this->_treeCacheRow = $treeCacheRow;
        parent::__construct();
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
        header('Content-Type: text/html; charset=utf-8');

        $benchmark = Vps_Benchmark::getInstance();
        $benchmark->startSequence('Seitenbaum');
        
        $componentId = $this->getTreeCacheRow()->component_id;

        $view = new Vps_View_Smarty_Cached();
        $view->url = $_SERVER['REQUEST_URI'];
        foreach ($decoratedPage->getTemplateVars() as $key => $val) {
            $view->$key = $val;
        }
        $view->component = $componentId;

        $benchmark->stopSequence('Seitenbaum');
        $result = $benchmark->getResults();
        $view->time = sprintf("%01.2f", $result['Seitenbaum']['duration']);
        echo $view->fetch('Master.html', $componentId);
        p($result['Seitenbaum']['duration']);
    }
}

