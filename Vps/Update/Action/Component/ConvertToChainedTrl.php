<?php
class Vps_Update_Action_Component_ConvertToChainedTrl extends Vps_Update_Action_Abstract
{
    public $rootPrefix = 'root-';
    public function update()
    {
        $db = Zend_Registry::get('db');
        $db->query("UPDATE vps_pages
            SET parent_id=CONCAT('root-master-', MID(parent_id, ".(strlen($this->rootPrefix)+1)."))
            WHERE parent_id LIKE ".$db->quote(str_replace('_', '\_', $this->rootPrefix).'%'));

        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
            $hasComponentId = false;
            foreach ($db->query("SHOW FIELDS FROM $table")->fetchAll() as $field) {
                if ($field['Field'] == 'component_id') $hasComponentId = true;
            }
            if ($hasComponentId) {
                $db->query("UPDATE $table SET component_id =
                        CONCAT('root-master-', MID(component_id, ".(strlen($this->rootPrefix)+1)."))
                        WHERE component_id LIKE ".$db->quote(str_replace('_', '\_', $this->rootPrefix).'%'));
            }
        }
        $db->query("UPDATE vpc_basic_text SET content =
                REPLACE(content, 'href=\"$this->rootPrefix', 'href=\"root-master-')");
        //TODO: Images

    }
}
