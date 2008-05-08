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

    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        if (!$vars['template']) {
            throw new Vpc_Exception(trlVps('Template not found for Component {0}',  get_class($this)));
        }
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
        $row = $this->getTreeCacheRow();
        $componentId = $row->component_id;
        echo Vps_View_Component::renderCachedComponent($row->component_id, true);
    }
}

