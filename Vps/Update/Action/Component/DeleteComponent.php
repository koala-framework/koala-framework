<?php
class Vps_Update_Action_Component_DeleteComponent extends Vps_Update_Action_Abstract
{
    public $id;

    public function checkSettings()
    {
        parent::checkSettings();
        if (!$this->id) {
            throw new Vps_ClientException("Required parameters: id");
        }
    }

    public function update()
    {
        $ids = $this->id;
        if (!is_array($ids)) $ids = array($ids);
        if (count($ids) < 10) {
            echo "\ndelete component ".implode(', ', $ids);
        } else {
            echo "\ndelete ".count($ids)." components";
        }

        $db = Zend_Registry::get('db');
        foreach ($db->query("SHOW TABLES")->fetchAll() as $table) {
            $table = array_values($table);
            $table = $table[0];
            $hasComponentId = false;
            foreach ($db->query("SHOW FIELDS FROM $table")->fetchAll() as $field) {
                if ($field['Field'] == 'component_id') $hasComponentId = true;
            }
            if ($hasComponentId) {
                $sql = "DELETE FROM $table WHERE 0";
                foreach ($ids as $id) {
                    $pattern = $id . '%';
                    $sql .= " OR component_id LIKE '".str_replace('_', '\_', $pattern)."' ";
                }
                $db->query($sql);
            }
        }
    }
}
