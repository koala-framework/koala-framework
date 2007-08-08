<?php
abstract class Vps_Controller_Action_Auto_Form_Vpc extends Vps_Controller_Action_Auto_Form
{
    public function preDispatch()
    {
        $pageId = $this->component->getDbId();
        $componentKey = $this->component->getComponentKey();
        
        // Parameter für _fetchData()
        $this->_setParam('page_id', $pageId);
        $this->_setParam('component_key', $componentKey);

        // Zeile wird in der Datenbank angelegt, falls es sie noch nicht gibt
        if (!$this->_fetchData()) {
            // Defaultwerte aus Setting auslesen
            $info = $this->_table->info();
            $insert = array();
            foreach ($info['cols'] as $col) {
                $setting = $this->component->getSetting($col);
                if ($setting) {
                    $insert[$col] = $this->component->getSetting($col);
                }
            }
            $insert['page_id'] = $pageId;
            $insert['component_key'] = $componentKey;
            $this->_table->insert($insert);
        }
    }
}
