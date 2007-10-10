<?php
class Vps_Auto_Vpc_Form extends Vps_Auto_Form
{
    public function __construct($component)
    {
        $this->setBodyStyle('padding: 10px 10px 0px 10px');

        $pageId = $component->getDbId();
        $componentKey = $component->getComponentKey();

        // Falls Eintrag nicht existiert, mit Defaultwerten eintragen
        $table = $component->getTable();
        $this->setTable($table);
        if ($table->find($pageId, $componentKey)->count() == 0) {
            $info = $table->info();
            $insert = array();
            foreach ($info['cols'] as $col) {
                $setting = $component->getSetting($col);
                if (!is_null($setting)) {
                    $insert[$col] = $component->getSetting($col);
                }
            }
            $insert['page_id'] = $pageId;
            $insert['component_key'] = $componentKey;
            $table->insert($insert);
        }

        $name = get_class($component);
        $id = array('page_id' => $pageId, 'component_key' => $componentKey);
        parent::__construct($name, $id);
    }
}
